<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat testimoni', only: ['index']),
            new Middleware('permission:hapus testimoni', only: ['destroy']),
            new Middleware('permission:aktifkan nonaktifkan testimoni', only: ['toggleStatus']),
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $testimonials = Testimonial::where('outlet_id', $user->outlet_id)
            ->latest()
            ->paginate(10);

        return view('testimonials.index', compact('testimonials'));
    }

    /**
     * Store a newly created testimonial (Public submission).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('testimonials', 'public');
        }

        // By default, testimonials are not published immediately for moderation
        $validated['is_published'] = false;

        Testimonial::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Testimoni Anda telah dikirim.',
        ]);
    }

    /**
     * Toggle published status.
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        // Ensure user owns this testimonial via outlet
        if ($testimonial->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $testimonial->is_published = ! $testimonial->is_published;
        $testimonial->save();

        return redirect()->back()->with('success', 'Status testimoni berhasil diperbarui.');
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Ensure user owns this testimonial via outlet
        if ($testimonial->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
