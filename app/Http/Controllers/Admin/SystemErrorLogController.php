<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class SystemErrorLogController extends Controller
{
    /**
     * Display a listing of system error logs.
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        
        $logs = [];
        
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            
            // Split by the start of a log entry
            $pattern = '/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/';
            $logEntries = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY);
            
            foreach ($logEntries as $entry) {
                // Parse the main log line
                preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*)/s', trim($entry), $matches);
                
                if (count($matches) === 5) {
                    $date = $matches[1];
                    $env = $matches[2];
                    $level = $matches[3];
                    $message = trim($matches[4]);
                    
                    // Filter by search
                    if ($request->filled('search')) {
                        $search = strtolower($request->search);
                        if (!str_contains(strtolower($message), $search) && !str_contains(strtolower($level), $search)) {
                            continue;
                        }
                    }
                    
                    // Filter by level
                    if ($request->filled('level') && strtolower($level) !== strtolower($request->level)) {
                        continue;
                    }
                    
                    $logs[] = [
                        'date' => $date,
                        'env' => $env,
                        'level' => $level,
                        'message' => $message,
                        'badge' => $this->getBadgeClass($level)
                    ];
                }
            }
        }
        
        // Reverse so newest is first
        $logs = array_reverse($logs);
        
        // Statistics
        $stats = [
            'total' => count($logs),
            'errors' => collect($logs)->whereIn('level', ['ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT'])->count(),
            'warnings' => collect($logs)->where('level', 'WARNING')->count(),
            'info' => collect($logs)->where('level', 'INFO')->count(),
        ];
        
        // Pagination
        $perPage = 15;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedLogs = new LengthAwarePaginator(
            array_slice($logs, $offset, $perPage),
            count($logs),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('admin.error-logs.index', compact('paginatedLogs', 'stats'));
    }

    /**
     * Show the detailed log message if needed.
     */
    public function show($id)
    {
        // Simple view setup if needed, but the index handles multiline decently
        return back();
    }

    /**
     * Clear the log file.
     */
    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }
        
        return back()->with('success', 'Log sistem berhasil dibersihkan.');
    }
    
    /**
     * Get badge color based on log level.
     */
    private function getBadgeClass($level)
    {
        $level = strtoupper($level);
        
        return match($level) {
            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => ['color' => 'red', 'label' => $level],
            'WARNING' => ['color' => 'orange', 'label' => $level],
            'NOTICE', 'INFO' => ['color' => 'blue', 'label' => $level],
            'DEBUG' => ['color' => 'gray', 'label' => $level],
            default => ['color' => 'gray', 'label' => $level],
        };
    }
}
