<?php

namespace App\Console\Commands;

use App\Jobs\FetchOsmDataJob;
use App\Services\OsmDataService;
use Illuminate\Console\Command;

class FetchOsmData extends Command
{
    protected $signature = 'heatmap:fetch-osm
                            {--area=jakarta : Predefined area (jakarta, surabaya, bandung, bali, yogyakarta, semarang, medan, makassar)}
                            {--bbox= : Custom bounding box as "south,west,north,east"}
                            {--queue : Dispatch to queue instead of running synchronously}
                            {--clear : Clear existing data for the area before fetching}';

    protected $description = 'Fetch business data from OpenStreetMap via Overpass API';

    public function handle(OsmDataService $service): int
    {
        $area = $this->option('area');
        $bboxStr = $this->option('bbox');
        $useQueue = $this->option('queue');
        $clear = $this->option('clear');

        $this->info('🗺️  CuanFlow Heatmap — OSM Data Fetcher');
        $this->newLine();

        // Show available areas
        if ($area === 'list') {
            $this->info('Available areas:');
            foreach ($service->getAvailableAreas() as $name) {
                $bbox = $service->getBoundingBox($name);
                $this->line("  • {$name} — [{$bbox[0]}, {$bbox[1]}, {$bbox[2]}, {$bbox[3]}]");
            }
            return self::SUCCESS;
        }

        // Clear existing data if requested
        if ($clear) {
            $this->warn("Clearing existing data for area '{$area}'...");
            $deleted = $service->clearArea($area);
            $this->info("Cleared {$deleted} existing records.");
            $this->newLine();
        }

        // Parse custom bbox if provided
        $customBbox = null;
        if ($bboxStr) {
            $parts = array_map('floatval', explode(',', $bboxStr));
            if (count($parts) !== 4) {
                $this->error('Invalid bbox format. Use: --bbox="south,west,north,east"');
                return self::FAILURE;
            }
            $customBbox = $parts;
            $this->info("Using custom bounding box: [{$bboxStr}]");
        } else {
            $bbox = $service->getBoundingBox($area);
            $this->info("Area: {$area}");
            $this->info("Bounding box: [{$bbox[0]}, {$bbox[1]}, {$bbox[2]}, {$bbox[3]}]");
        }

        $this->newLine();

        // Dispatch to queue or run synchronously
        if ($useQueue) {
            FetchOsmDataJob::dispatch($area, $customBbox);
            $this->info('✅ Job dispatched to queue. Run `php artisan queue:work` to process.');
            return self::SUCCESS;
        }

        $this->info('Fetching data from Overpass API... (this may take a few minutes)');

        $bar = $this->output->createProgressBar(3);
        $bar->start();

        $bar->advance(); // Step 1: Querying API

        if ($customBbox) {
            $result = $service->fetchAndStoreCustom(...$customBbox);
        } else {
            $result = $service->fetchAndStore($area);
        }

        $bar->advance(); // Step 2: Storing data
        $bar->advance(); // Step 3: Complete
        $bar->finish();

        $this->newLine(2);

        $this->info("✅ Fetch complete!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Elements fetched', number_format($result['fetched'])],
                ['Business points stored', number_format($result['stored'])],
            ]
        );

        $this->newLine();
        if ($customBbox) {
            $bboxStrForCalc = implode(',', $customBbox);
        } else {
            $bbox = $service->getBoundingBox($area);
            $bboxStrForCalc = implode(',', $bbox);
        }
        $this->info("💡 Next: run `php artisan heatmap:calculate --bounds=\"{$bboxStrForCalc}\"` to generate opportunity scores.");

        return self::SUCCESS;
    }
}
