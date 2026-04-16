<?php

namespace App\Jobs;

use App\Services\AIAnalyzerService;
use App\Services\GridCalculationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateGridScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public int $tries = 2;

    private bool $withAI;

    /**
     * @param  bool  $withAI  Whether to run AI classification after calculation
     */
    public function __construct(bool $withAI = true)
    {
        $this->withAI = $withAI;
    }

    public function handle(GridCalculationService $gridService, AIAnalyzerService $aiService): void
    {
        Log::info('CalculateGridScoresJob: Starting grid calculation');

        $gridResult = $gridService->calculateAll();

        Log::info('CalculateGridScoresJob: Grid calculation complete', $gridResult);

        if ($this->withAI && $gridResult['grids_with_data'] > 0) {
            Log::info('CalculateGridScoresJob: Starting AI classification');
            $aiResult = $aiService->classifyAllGrids();
            Log::info('CalculateGridScoresJob: AI classification complete', $aiResult);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CalculateGridScoresJob: Failed — '.$exception->getMessage());
    }
}
