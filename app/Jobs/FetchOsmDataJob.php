<?php

namespace App\Jobs;

use App\Services\OsmDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchOsmDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 3;

    public int $backoff = 30;

    private string $area;

    private ?array $customBbox;

    /**
     * Create a new job instance.
     *
     * @param string     $area       Predefined area name (e.g., 'jakarta')
     * @param array|null $customBbox Custom bounding box [south, west, north, east]
     */
    public function __construct(string $area = 'jakarta', ?array $customBbox = null)
    {
        $this->area = $area;
        $this->customBbox = $customBbox;
    }

    public function handle(OsmDataService $service): void
    {
        Log::info("FetchOsmDataJob: Starting for area '{$this->area}'");

        if ($this->customBbox) {
            $result = $service->fetchAndStoreCustom(...$this->customBbox);
        } else {
            $result = $service->fetchAndStore($this->area);
        }

        Log::info("FetchOsmDataJob: Complete", $result);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("FetchOsmDataJob: Failed — " . $exception->getMessage());
    }
}
