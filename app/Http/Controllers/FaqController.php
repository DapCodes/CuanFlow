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
        $outlet = Auth::user()->outlet;

        if (!$outlet) {
            return redirect()->route('outlets.register.index');
        }

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

        $faqs = $query->ordered()->paginate(15);

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
        $faq->markHelpful();
        return response()->json([
            'success' => true,
            'helpful_count' => $faq->helpful_count,
        ]);
    }

    public function markNotHelpful(Faq $faq)
    {
        $faq->markNotHelpful();
        return response()->json([
            'success' => true,
            'not_helpful_count' => $faq->not_helpful_count,
        ]);
    }
}