<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\ClaraAiService;
use App\Models\Outlet;

// Register command untuk generate insights
Artisan::command('ai:generate-insights {--outlet_id=}', function (ClaraAiService $claraAi) {
    $outletId = $this->option('outlet_id');

    if ($outletId) {
        $outlets = Outlet::where('id', $outletId)->get();
    } else {
        $outlets = Outlet::all();
    }

    foreach ($outlets as $outlet) {
        $this->info("Generating insights for outlet: {$outlet->name}");
        
        try {
            $claraAi->generateDailyInsights($outlet->id);
            $this->info("✓ Insights generated successfully for {$outlet->name}");
        } catch (\Exception $e) {
            $this->error("✗ Failed for {$outlet->name}: {$e->getMessage()}");
        }
    }

    $this->info('Done!');
})->purpose('Generate daily AI insights for outlets');

// Schedule: Generate insights setiap hari jam 8 pagi WIB
Schedule::command('ai:generate-insights')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
