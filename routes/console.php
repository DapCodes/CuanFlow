<?php

use App\Models\Outlet;
use App\Services\ClaraAiService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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

// Schedule: Automated backup setiap hari jam 2 pagi WIB
Schedule::command('backup:run')
    ->dailyAt(config('cuanflow-backup.scheduled_time', '02:00'))
    ->timezone(config('cuanflow-backup.timezone', 'Asia/Jakarta'))
    ->withoutOverlapping()
    ->onOneServer();

// Schedule: Cleanup backup lama setiap Minggu jam 3 pagi WIB
Schedule::command('backup:clean')
    ->weeklyOn(0, '03:00')
    ->timezone(config('cuanflow-backup.timezone', 'Asia/Jakarta'))
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
