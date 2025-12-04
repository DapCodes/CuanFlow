<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use Illuminate\Http\Request;

class AiInsightController extends Controller
{
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

        return view('ai-insights.index', compact('insights', 'unreadCount'));
    }

    public function show($id)
    {
        $insight = AiInsight::where('outlet_id', auth()->user()->outlet_id)
            ->findOrFail($id);

        // Mark as read
        $insight->markAsRead();

        return view('ai-insights.show', compact('insight'));
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            if (!$request->user()) {
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
            if (!$request->user()) {
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
            if (!$request->user()) {
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
}