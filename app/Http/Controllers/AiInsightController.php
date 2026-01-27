<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class AiInsightController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat ai insights', only: ['index', 'daily']),
            new Middleware('permission:lihat detail ai insight', only: ['show']),
            new Middleware('permission:tandai ai insight dibaca', only: ['markAsRead']),
            new Middleware('permission:abaikan ai insight', only: ['dismiss']),
            new Middleware('permission:tandai semua ai insight dibaca', only: ['markAllAsRead']),
            new Middleware('permission:lihat kalender ai insight', only: ['calendarSummary']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = AiInsight::where('outlet_id', $user->outlet_id)
            ->active()
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        $insights = $query->paginate(20);

        // Count unread
        $unreadCount = AiInsight::where('outlet_id', $user->outlet_id)
            ->unread()
            ->active()
            ->count();

        return view('main.ai-insights.index', compact('insights', 'unreadCount'));
    }

    public function show($id)
    {
        $insight = AiInsight::where('outlet_id', auth()->user()->outlet_id)
            ->findOrFail($id);

        // Mark as read
        $insight->markAsRead();

        return view('main.ai-insights.show', compact('insight'));
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            if (! $request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $insight = AiInsight::where('outlet_id', $request->user()->outlet_id)
                ->active()
                ->findOrFail($id);

            $insight->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Insight ditandai sudah dibaca',
            ]);
        } catch (\Throwable $e) {
            Log::error('markAsRead error', [
                'id' => $id,
                'user_id' => optional($request->user())->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menandai insight',
            ], 500);
        }
    }

    public function dismiss(Request $request, $id)
    {
        try {
            if (! $request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $insight = AiInsight::where('outlet_id', $request->user()->outlet_id)
                ->active()
                ->findOrFail($id);

            $insight->dismiss();

            return response()->json([
                'success' => true,
                'message' => 'Insight berhasil di-dismiss',
            ]);
        } catch (\Throwable $e) {
            Log::error('dismissInsight error', [
                'id' => $id,
                'user_id' => optional($request->user())->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat dismiss insight',
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            if (! $request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            AiInsight::where('outlet_id', $request->user()->outlet_id)
                ->unread()
                ->active()
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua insight telah ditandai sebagai dibaca',
            ]);
        } catch (\Throwable $e) {
            Log::error('markAllAsRead error', [
                'user_id' => optional($request->user())->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menandai semua insight',
            ], 500);
        }
    }

    public function calendarSummary(Request $request)
    {
        $user = $request->user();

        // FullCalendar kirim ISO string, kadang "+" berubah jadi spasi.
        $startRaw = (string) $request->query('start', '');
        $endRaw = (string) $request->query('end', '');

        $startRaw = str_replace(' ', '+', $startRaw);
        $endRaw = str_replace(' ', '+', $endRaw);

        try {
            $startDate = $startRaw ? Carbon::parse($startRaw)->startOfDay() : now()->startOfMonth();
            $endDate = $endRaw ? Carbon::parse($endRaw)->endOfDay() : now()->endOfMonth();
        } catch (\Throwable $e) {
            Log::error('calendarSummary parse error', [
                'start' => $startRaw,
                'end' => $endRaw,
                'message' => $e->getMessage(),
            ]);

            // balikin array kosong biar FullCalendar gak crash
            return response()->json([]);
        }

        $rows = AiInsight::query()
            ->where('outlet_id', $user->outlet_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
            DATE(created_at) as d,
            SUM(CASE WHEN is_dismissed = 0 AND is_read = 0 THEN 1 ELSE 0 END) as unread_count,
            SUM(CASE WHEN is_dismissed = 0 AND is_read = 1 THEN 1 ELSE 0 END) as read_count,
            SUM(CASE WHEN is_dismissed = 1 THEN 1 ELSE 0 END) as dismissed_count
        ')
            ->groupBy('d')
            ->get();

        $events = $rows->map(function ($r) {
            $unread = (int) $r->unread_count;
            $read = (int) $r->read_count;
            $dismissed = (int) $r->dismissed_count;

            $titleParts = [];
            if ($unread) {
                $titleParts[] = "U:$unread";
            }
            if ($read) {
                $titleParts[] = "R:$read";
            }
            if ($dismissed) {
                $titleParts[] = "D:$dismissed";
            }

            return [
                'title' => implode(' ', $titleParts),
                'start' => $r->d,     // YYYY-MM-DD OK utk allDay
                'allDay' => true,
                'extendedProps' => [
                    'unread' => $unread,
                    'read' => $read,
                    'dismissed' => $dismissed,
                ],
            ];
        })->values();

        return response()->json($events);
    }

    public function daily(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date'); // YYYY-MM-DD

        $selected = $date ? Carbon::parse($date)->toDateString() : today()->toDateString();

        $insights = AiInsight::query()
            ->where('outlet_id', $user->outlet_id)
            ->whereDate('created_at', $selected)
            ->orderByRaw("FIELD(severity,'critical','warning','info')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($i) {
                return [
                    'id' => $i->id,
                    'title' => $i->title,
                    'type' => $i->type,
                    'severity' => $i->severity,
                    'content' => $i->content,
                    'is_read' => (bool) $i->is_read,
                    'is_dismissed' => (bool) $i->is_dismissed,
                    'time' => $i->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'date' => $selected,
            'counts' => [
                'total' => $insights->count(),
                'unread' => $insights->where('is_dismissed', false)->where('is_read', false)->count(),
                'read' => $insights->where('is_dismissed', false)->where('is_read', true)->count(),
                'dismissed' => $insights->where('is_dismissed', true)->count(),
            ],
            'insights' => $insights,
        ]);
    }
}
