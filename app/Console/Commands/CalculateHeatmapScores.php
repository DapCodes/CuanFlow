<?php

namespace App\Console\Commands;

use App\Jobs\CalculateGridScoresJob;
use App\Models\BusinessPoint;
use App\Models\GridArea;
use App\Services\AIAnalyzerService;
use App\Services\GridCalculationService;
use Illuminate\Console\Command;

class CalculateHeatmapScores extends Command
{
    protected $signature = 'heatmap:calculate
                            {--no-ai : Skip AI classification}
                            {--queue : Dispatch to queue instead of running synchronously}';

    protected $description = 'Calculate opportunity scores for heatmap grid areas';

    public function handle(GridCalculationService $gridService, AIAnalyzerService $aiService): int
    {
        $skipAI = $this->option('no-ai');
        $useQueue = $this->option('queue');

        $this->info('📊 CuanFlow Heatmap — Score Calculator');
        $this->newLine();

        // Check for business data
        $totalPoints = BusinessPoint::count();
        if ($totalPoints === 0) {
            $this->error('No business points found! Run `php artisan heatmap:fetch-osm` first.');
            return self::FAILURE;
        }

        $this->info("Business points in database: " . number_format($totalPoints));
        $this->newLine();

        // Dispatch to queue if requested
        if ($useQueue) {
            CalculateGridScoresJob::dispatch(!$skipAI);
            $this->info('✅ Job dispatched to queue. Run `php artisan queue:work` to process.');
            return self::SUCCESS;
        }

        // Step 1: Grid Calculation
        $this->info('Step 1: Generating grid cells and calculating scores...');

        $gridResult = $gridService->calculateAll();

        $this->info("  • Grid cells created: " . number_format($gridResult['grids_created']));
        $this->info("  • Grids with businesses: " . number_format($gridResult['grids_with_data']));
        $this->newLine();

        // Step 2: AI Classification
        if (!$skipAI && $gridResult['grids_with_data'] > 0) {
            $this->info('Step 2: Running AI classification...');

            $aiResult = $aiService->classifyAllGrids();

            $this->info("  • Classified: " . $aiResult['classified']);
            $this->info("  • Fallback/Failed: " . $aiResult['failed']);
            $this->newLine();
        } elseif ($skipAI) {
            $this->warn('Step 2: AI classification skipped (--no-ai flag).');
            $this->newLine();
        }

        // Summary
        $this->info('✅ Calculation complete!');
        $this->newLine();

        // Show distribution
        $highCount = GridArea::where('ai_classification', 'High Potential')->count();
        $medCount = GridArea::where('ai_classification', 'Medium')->count();
        $lowCount = GridArea::where('ai_classification', 'Low')->count();
        $unclassified = GridArea::whereNull('ai_classification')->count();

        $this->table(
            ['Classification', 'Count'],
            [
                ['🟢 High Potential', number_format($highCount)],
                ['🟡 Medium', number_format($medCount)],
                ['🔴 Low', number_format($lowCount)],
                ['⚪ Unclassified', number_format($unclassified)],
            ]
        );

        $this->newLine();
        $this->info('💡 Access the data via GET /api/v1/heatmap');

        return self::SUCCESS;
    }
}
