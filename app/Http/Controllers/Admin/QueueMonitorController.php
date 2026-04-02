<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class QueueMonitorController extends Controller
{
    public function index()
    {
        $pendingJobsCount = DB::table('jobs')->whereNull('reserved_at')->count();
        $runningJobsCount = DB::table('jobs')->whereNotNull('reserved_at')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();

        // Get Oldest Pending Job to measure delay
        $oldestJob = DB::table('jobs')->whereNull('reserved_at')->orderBy('available_at', 'asc')->first();
        $queueDelaySeconds = 0;
        
        if ($oldestJob) {
            $now = time();
            $availableAt = $oldestJob->available_at;
            $queueDelaySeconds = max(0, $now - $availableAt);
        }

        // Get Worker Status gracefully
        $workerStatus = 'Unknown';
        $workerCount = 0;
        if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            try {
                $processList = shell_exec('ps aux | grep "queue:work" | grep -v grep');
                $workerCount = substr_count($processList, 'queue:work');
                $workerStatus = $workerCount > 0 ? 'Active' : 'Inactive';
            } catch (\Exception $e) {
                $workerStatus = 'Permission Denied';
            }
        }

        $pendingJobs = DB::table('jobs')->whereNull('reserved_at')->orderBy('id', 'desc')->take(10)->get();
        $runningJobs = DB::table('jobs')->whereNotNull('reserved_at')->orderBy('id', 'desc')->take(10)->get();
        $failedJobs = DB::table('failed_jobs')->orderBy('id', 'desc')->paginate(15);

        return view('admin.security.queue.index', compact(
            'pendingJobsCount', 'runningJobsCount', 'failedJobsCount',
            'queueDelaySeconds', 'workerStatus', 'workerCount',
            'pendingJobs', 'runningJobs', 'failedJobs'
        ));
    }

    public function retry($id)
    {
        Artisan::call('queue:retry', ['id' => $id]);
        return back()->with('success', "Job dengan UUID $id berhasil dijadwalkan ulang (retry).");
    }

    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => 'all']);
        return back()->with('success', 'Semua Failed Jobs berhasil dijadwalkan ulang.');
    }

    public function destroy($id)
    {
        Artisan::call('queue:forget', ['id' => $id]);
        return back()->with('success', "Failed Job dengan UUID $id berhasil dihapus secara permanen.");
    }

    public function flush()
    {
        Artisan::call('queue:flush');
        return back()->with('success', 'Semua Failed Jobs telah dihapus secara permanen.');
    }
}
