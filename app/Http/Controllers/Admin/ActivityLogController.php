<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BackupLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityLogController extends Controller
{
    /**
     * Display the activity log index with filters.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer'])->latest();

        // Filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('log_name')) {
            $query->byLogName($request->log_name);
        }

        if ($request->filled('causer_id')) {
            $query->byCauser($request->causer_id);
        }

        if ($request->filled('event')) {
            $query->byEvent($request->event);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        // Stats
        $stats = [
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total' => Activity::count(),
            'top_user' => Activity::whereNotNull('causer_id')
                ->where('causer_type', User::class)
                ->selectRaw('causer_id, count(*) as count')
                ->groupBy('causer_id')
                ->orderByDesc('count')
                ->first(),
        ];

        if ($stats['top_user']) {
            $stats['top_user']->user = User::withTrashed()->find($stats['top_user']->causer_id);
        }

        $logs = $query->paginate(20)->appends($request->query());
        $logNames = Activity::distinct('log_name')->pluck('log_name')->filter()->sort();
        $users = User::withTrashed()->orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact('logs', 'stats', 'logNames', 'users'));
    }

    /**
     * Show a specific activity log detail.
     */
    public function show($id)
    {
        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        return view('admin.activity-logs.show', compact('log'));
    }

    /**
     * List archived log backups.
     */
    public function archives()
    {
        $archives = BackupLog::where('type', 'database')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.activity-logs.archives', compact('archives'));
    }

    /**
     * Download an archived log file.
     */
    public function downloadArchive($id)
    {
        $backup = BackupLog::where('type', 'database')->findOrFail($id);

        if (! Storage::exists($backup->path)) {
            return back()->with('error', 'File arsip tidak ditemukan.');
        }

        return Storage::download($backup->path, $backup->filename);
    }
}
