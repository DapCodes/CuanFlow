<?php

namespace App\Services;

use App\Models\GridArea;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalyzerService
{
    private ?string $apiKey;

    private string $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        // Use AI_API_KEY first, fall back to CLARA_AI_API_KEY
        $this->apiKey = config('services.heatmap.ai_key')
            ?? config('services.clara.key');
    }

    /**
     * Analyze and classify all unclassified grid areas using AI
     */
    public function classifyAllGrids(): array
    {
        $grids = GridArea::whereNull('ai_classification')
            ->where('total_businesses', '>', 0)
            ->orderBy('opportunity_score', 'desc')
            ->get();

        if ($grids->isEmpty()) {
            Log::info('AIAnalyzer: No unclassified grids to process');
            return ['classified' => 0, 'failed' => 0];
        }

        Log::info("AIAnalyzer: Processing {$grids->count()} grids");

        $classified = 0;
        $failed = 0;

        // Process in batches of 20 to minimize API calls
        $batches = $grids->chunk(20);

        foreach ($batches as $batch) {
            $result = $this->classifyBatch($batch);
            $classified += $result['classified'];
            $failed += $result['failed'];

            // Small delay between batches to respect rate limits
            if ($batches->count() > 1) {
                usleep(500000); // 500ms
            }
        }

        Log::info("AIAnalyzer: Complete — {$classified} classified, {$failed} failed");

        return compact('classified', 'failed');
    }

    /**
     * Classify a batch of grid areas using a single AI call
     */
    private function classifyBatch($grids): array
    {
        if (!$this->apiKey) {
            // Fallback: use rule-based classification
            return $this->classifyBatchRuleBased($grids);
        }

        $gridDataText = $this->formatGridDataForAI($grids);

        $prompt = <<<PROMPT
You are a business location analyst for Indonesia. Given the following grid area data with business metrics, classify each area's business opportunity potential.

For each grid area, provide:
1. Classification: "High Potential", "Medium", or "Low"
2. Brief reason (1 sentence)

Criteria:
- "High Potential": High demand score with low competition, OR diverse category mix with moderate traffic. Score typically > 60.
- "Medium": Balanced competition and demand, moderate diversity. Score typically 30-60.
- "Low": Oversaturated market OR very low activity. Score typically < 30.

Grid Data:
{$gridDataText}

Respond in JSON format only:
[
  {"id": 1, "classification": "High Potential", "reason": "..."},
  {"id": 2, "classification": "Medium", "reason": "..."}
]
PROMPT;

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'CuanFlow Heatmap',
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', [
                'model' => 'openrouter/free',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a business analyst. Respond only in valid JSON arrays.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 3000,
            ]);

            if (!$response->successful()) {
                Log::warning('AIAnalyzer: API request failed', ['status' => $response->status()]);
                return $this->classifyBatchRuleBased($grids);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                Log::warning('AIAnalyzer: API returned error', ['error' => $data['error']]);
                return $this->classifyBatchRuleBased($grids);
            }

            $content = $data['choices'][0]['message']['content'] ?? '';

            return $this->parseAndApplyAIResults($grids, $content);

        } catch (\Exception $e) {
            Log::error('AIAnalyzer: Exception — ' . $e->getMessage());
            return $this->classifyBatchRuleBased($grids);
        }
    }

    /**
     * Format grid data as text for the AI prompt
     */
    private function formatGridDataForAI($grids): string
    {
        $lines = [];

        foreach ($grids as $grid) {
            $lines[] = "ID:{$grid->id} | "
                . "Score:{$grid->opportunity_score} | "
                . "Businesses:{$grid->total_businesses} | "
                . "Diversity:{$grid->category_diversity} | "
                . "Competition:{$grid->competition_score} | "
                . "Demand:{$grid->demand_score} | "
                . "Lat:{$grid->center_lat}, Lng:{$grid->center_lng}";
        }

        return implode("\n", $lines);
    }

    /**
     * Parse AI JSON response and update grid records
     */
    private function parseAndApplyAIResults($grids, string $aiContent): array
    {
        $classified = 0;
        $failed = 0;

        // Extract JSON from the response (handle markdown code blocks)
        $jsonContent = $aiContent;
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $aiContent, $matches)) {
            $jsonContent = $matches[1];
        }

        $results = json_decode(trim($jsonContent), true);

        if (!is_array($results)) {
            Log::warning('AIAnalyzer: Could not parse AI response as JSON', [
                'content' => substr($aiContent, 0, 500),
            ]);
            return $this->classifyBatchRuleBased($grids);
        }

        // Create a map of results by ID
        $resultMap = [];
        foreach ($results as $result) {
            if (isset($result['id'], $result['classification'])) {
                $resultMap[$result['id']] = $result;
            }
        }

        foreach ($grids as $grid) {
            if (isset($resultMap[$grid->id])) {
                $result = $resultMap[$grid->id];
                $classification = $this->normalizeClassification($result['classification']);

                $grid->update([
                    'ai_classification' => $classification,
                    'ai_analysis' => $result['reason'] ?? null,
                ]);

                $classified++;
            } else {
                // Fallback for grids not in AI response
                $classification = $this->ruleBasedClassification($grid->opportunity_score);
                $grid->update([
                    'ai_classification' => $classification,
                    'ai_analysis' => 'Rule-based classification (AI did not return result for this grid)',
                ]);
                $failed++;
            }
        }

        return compact('classified', 'failed');
    }

    /**
     * Normalize classification strings
     */
    private function normalizeClassification(string $classification): string
    {
        $normalized = strtolower(trim($classification));

        if (str_contains($normalized, 'high')) {
            return 'High Potential';
        }

        if (str_contains($normalized, 'medium') || str_contains($normalized, 'moderate')) {
            return 'Medium';
        }

        return 'Low';
    }

    /**
     * Rule-based fallback classification
     */
    private function classifyBatchRuleBased($grids): array
    {
        $classified = 0;

        foreach ($grids as $grid) {
            $classification = $this->ruleBasedClassification($grid->opportunity_score);

            $analysis = $this->generateRuleBasedAnalysis($grid, $classification);

            $grid->update([
                'ai_classification' => $classification,
                'ai_analysis' => $analysis,
            ]);

            $classified++;
        }

        return ['classified' => $classified, 'failed' => 0];
    }

    /**
     * Simple rule-based classification by score
     */
    private function ruleBasedClassification(float $score): string
    {
        if ($score >= 60) {
            return 'High Potential';
        }

        if ($score >= 30) {
            return 'Medium';
        }

        return 'Low';
    }

    /**
     * Generate a human-readable analysis for rule-based classification
     */
    private function generateRuleBasedAnalysis(GridArea $grid, string $classification): string
    {
        $parts = [];

        $parts[] = "Area contains {$grid->total_businesses} business(es) across {$grid->category_diversity} unique category(ies).";

        if ($classification === 'High Potential') {
            $parts[] = 'Demand exceeds competition — strong potential for new businesses.';
        } elseif ($classification === 'Medium') {
            $parts[] = 'Balanced market with moderate competition and demand.';
        } else {
            if ($grid->total_businesses === 0) {
                $parts[] = 'Minimal business activity detected in this area.';
            } else {
                $parts[] = 'Market appears saturated relative to diversity.';
            }
        }

        return implode(' ', $parts);
    }
}
