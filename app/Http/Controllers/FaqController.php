<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FaqController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat faq', only: ['index', 'show']),
            new Middleware('permission:tandai faq membantu', only: ['markHelpful']),
            new Middleware('permission:tandai faq tidak membantu', only: ['markNotHelpful']),
        ];
    }

    public function index(Request $request)
    {
        $query = Faq::where('is_active', true);

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->ordered()
            ->with(['currentUserVote'])  // Eager load current user's vote
            ->paginate(15);

        return view('faqs.index', compact('faqs'));
    }

    public function show(Faq $faq)
    {
        if (!$faq->is_active) {
            abort(404);
        }

        $faq->incrementViewCount();
        return view('faqs.show', compact('faq'));
    }

    public function markHelpful(Faq $faq)
    {
        $user = Auth::user();
        $vote = $faq->votes()->where('user_id', $user->id)->first();

        if ($vote) {
            if ($vote->is_helpful) {
                // Determine if we should toggle off or keep it. 
                // User requirement: "1 user hanya bisa memilih 1 di setiap FAQ"
                // Usually clicking again might unvote, or do nothing.
                // Let's implement: if already helpful, unvote (remove vote).
                $vote->delete();
                $faq->decrement('helpful_count');
                $status = 'removed';
            } else {
                // Switching from not helpful to helpful
                $vote->update(['is_helpful' => true]);
                $faq->decrement('not_helpful_count');
                $faq->increment('helpful_count');
                $status = 'switched';
            }
        } else {
            // Create new helpful vote
            $faq->votes()->create([
                'user_id' => $user->id,
                'is_helpful' => true,
            ]);
            $faq->increment('helpful_count');
            $status = 'added';
        }

        return response()->json([
            'success' => true,
            'helpful_count' => $faq->helpful_count,
            'not_helpful_count' => $faq->not_helpful_count,
            'status' => $status,
            'type' => 'helpful'
        ]);
    }

    public function markNotHelpful(Faq $faq)
    {
        $user = Auth::user();
        $vote = $faq->votes()->where('user_id', $user->id)->first();

        if ($vote) {
            if (!$vote->is_helpful) {
                // Already not helpful, unvote (remove vote)
                $vote->delete();
                $faq->decrement('not_helpful_count');
                $status = 'removed';
            } else {
                // Switching from helpful to not helpful
                $vote->update(['is_helpful' => false]);
                $faq->decrement('helpful_count');
                $faq->increment('not_helpful_count');
                $status = 'switched';
            }
        } else {
            // Create new not helpful vote
            $faq->votes()->create([
                'user_id' => $user->id,
                'is_helpful' => false,
            ]);
            $faq->increment('not_helpful_count');
            $status = 'added';
        }

        return response()->json([
            'success' => true,
            'helpful_count' => $faq->helpful_count,
            'not_helpful_count' => $faq->not_helpful_count,
            'status' => $status,
            'type' => 'not_helpful'
        ]);
    }
}