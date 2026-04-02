<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class SystemErrorLogController extends Controller
{
    /**
     * Display a listing of system error logs.
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        
        $logs = $this->parseLogFile($logPath, $request);
        
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
     * Clear the log file without backing up.
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
     * Backup the log file to JSON format and clear it.
     */
    public function backup()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!File::exists($logPath) || filesize($logPath) === 0) {
                return back()->with('error', 'Tidak ada log untuk di-backup.');
            }

            // Parse all logs without filters
            $request = new Request();
            $logs = $this->parseLogFile($logPath, $request);

            if (empty($logs)) {
                return back()->with('error', 'Format log kosong atau tidak valid untuk di-backup.');
            }

            $fileName = 'system_error_log_' . now()->format('Y_m_d_His') . '.json';
            $path = 'backups/error_logs/' . $fileName;
            
            // Store JSON file
            $jsonContent = json_encode(array_reverse($logs), JSON_PRETTY_PRINT);
            Storage::put($path, $jsonContent);

            // Record in BackupLog
            BackupLog::create([
                'filename' => $fileName,
                'disk' => 'local',
                'path' => $path,
                'size' => Storage::size($path),
                'type' => 'system_error_log',
                'status' => 'completed',
                'created_by' => auth()->id()
            ]);

            // Clear original file
            File::put($logPath, '');

            return back()->with('success', 'Error log berhasil di-backup ke format JSON dan file log asli dibersihkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup: ' . $e->getMessage());
        }
    }

    /**
     * List archived error log backups.
     */
    public function archives()
    {
        $archives = BackupLog::where('type', 'system_error_log')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.error-logs.archives', compact('archives'));
    }

    /**
     * View JSON content of an archive.
     */
    public function viewArchive($id)
    {
        $backup = BackupLog::where('type', 'system_error_log')->findOrFail($id);

        if (!Storage::exists($backup->path)) {
            return back()->with('error', 'File arsip tidak ditemukan.');
        }

        try {
            $content = Storage::get($backup->path);
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Format file JSON tidak valid.');
            }

            return view('admin.error-logs.view-archive', compact('backup', 'json'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file arsip: ' . $e->getMessage());
        }
    }

    /**
     * Download an archived log file.
     */
    public function downloadArchive($id)
    {
        $backup = BackupLog::where('type', 'system_error_log')->findOrFail($id);

        if (!Storage::exists($backup->path)) {
            return back()->with('error', 'File arsip tidak ditemukan.');
        }

        return Storage::download($backup->path, $backup->filename);
    }
    
    /**
     * Helper to parse physical log file.
     */
    private function parseLogFile($path, Request $request)
    {
        $logs = [];
        
        if (File::exists($path)) {
            $content = File::get($path);
            
            $pattern = '/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/';
            $logEntries = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY);
            
            foreach ($logEntries as $entry) {
                preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*)/s', trim($entry), $matches);
                
                if (count($matches) === 5) {
                    $date = $matches[1];
                    $env = $matches[2];
                    $level = $matches[3];
                    $message = trim($matches[4]);
                    
                    if ($request->filled('search')) {
                        $search = strtolower($request->search);
                        if (!str_contains(strtolower($message), $search) && !str_contains(strtolower($level), $search)) {
                            continue;
                        }
                    }
                    
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
        
        return $logs;
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
