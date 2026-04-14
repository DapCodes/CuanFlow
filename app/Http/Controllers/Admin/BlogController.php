<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Blog::count(),
            'published' => Blog::where('is_published', true)->count(),
            'draft' => Blog::where('is_published', false)->count(),
            'total_views' => Blog::sum('views'),
        ];

        $query = Blog::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.blogs.index', compact('blogs', 'stats'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'category' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = uniqid('blog_') . '.webp';
            $path = 'blogs/' . $filename;
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists('blogs')) {
                Storage::disk('public')->makeDirectory('blogs');
            }

            // Compress & resize image using Intervention Image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scaleDown(width: 1200);
            
            // Save as compressed webp
            $image->toWebp(quality: 80)->save(storage_path('app/public/' . $path));
            
            $validated['thumbnail'] = $path;
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['is_published'] = $request->has('is_published');

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog berhasil ditambahkan.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'category' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            
            $file = $request->file('thumbnail');
            $filename = uniqid('blog_') . '.webp';
            $path = 'blogs/' . $filename;
            
            if (!Storage::disk('public')->exists('blogs')) {
                Storage::disk('public')->makeDirectory('blogs');
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scaleDown(width: 1200);
            $image->toWebp(quality: 80)->save(storage_path('app/public/' . $path));
            
            $validated['thumbnail'] = $path;
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['is_published'] = $request->has('is_published');

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog berhasil diperbarui.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog berhasil dihapus.');
    }

    public function toggleStatus(Blog $blog)
    {
        $blog->update(['is_published' => !$blog->is_published]);
        return back()->with('success', 'Status publikasi berhasil diubah.');
    }
}
