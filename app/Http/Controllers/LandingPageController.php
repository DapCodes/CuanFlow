<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $outlet = null;
        
        if ($user->outlet_id) {
            $outlet = Outlet::with('landingPage')->find($user->outlet_id);
            // Verify/Create landing page if not exists
            if ($outlet && !$outlet->landingPage) {
                $outlet->landingPage()->create();
                $outlet->load('landingPage');
            }
        }
        
        return view('landing.index', compact('outlet'));
    }

    public function show($id, $slug = null)
    {
        $landingPage = LandingPage::with('outlet')->where('outlet_id', $id)->firstOrFail();
        $outlet = $landingPage->outlet;

        // Redirect to cool URL if slug is missing or doesn't match
        $correctSlug = Str::slug($outlet->name);
        if ($slug !== $correctSlug) {
            return redirect()->route('landing-pages.show', [$id, $correctSlug]);
        }
        
        // Calculate simplified sales count integers (e.g. 152 -> 150+)
        $salesCount = $outlet->sales()->count();
        $displaySales = $this->formatNumber($salesCount);

        // Get selected products
        $products = [];
        if ($landingPage->selected_product_ids) {
            $products = Product::whereIn('id', $landingPage->selected_product_ids)->get();
        }

        // Get selected testimonials
        $testimonials = [];
        if ($landingPage->selected_testimonial_ids) {
            $testimonials = Testimonial::whereIn('id', $landingPage->selected_testimonial_ids)
                                       ->where('is_published', true)
                                       ->get();
        }

        return view('landing.show', compact('landingPage', 'outlet', 'displaySales', 'products', 'testimonials'));
    }

    public function edit($id)
    {
        $outlet = Outlet::with('landingPage')->findOrFail($id);
        
        // Ensure landing page entry exists
        $landingPage = $outlet->landingPage ?? $outlet->landingPage()->create();
        
        $landingPage = $outlet->landingPage ?? $outlet->landingPage()->create();
        
        $products = Product::where('outlet_id', $id)->get();
        
        // Fetch published testimonials for selection
        $testimonials = Testimonial::where('outlet_id', $id)
                                   ->where('is_published', true)
                                   ->latest()
                                   ->get();

        return view('landing.edit', compact('outlet', 'landingPage', 'products', 'testimonials'));
    }

    public function update(Request $request, $id)
    {
        $outlet = Outlet::findOrFail($id);
        $landingPage = $outlet->landingPage;

        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|max:5120',
            'about_image' => 'nullable|image|max:5120',
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'required|string|max:20',
            'about_text' => 'nullable|string',
            'vision_text' => 'nullable|string',
            'mission_text' => 'nullable|string',
            'tagline_text' => 'nullable|string',
            'services_section' => 'nullable|array',
            'testimonials_section' => 'nullable|array',
            'gallery_images' => 'nullable|array',
            'cta_text' => 'nullable|string',
            'cta_button_text' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:20',
            'footer_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'selected_product_ids' => 'nullable|array',
            'selected_testimonial_ids' => 'nullable|array',
            'social_media' => 'nullable|array',
        ]);

        if ($request->hasFile('hero_image')) {
            // Delete old image
            if ($landingPage->hero_image) {
                Storage::disk('public')->delete($landingPage->hero_image);
            }
            
            // Compress and store image
            $data['hero_image'] = $this->compressAndStoreImage($request->file('hero_image'), 'landing-pages');
        }

        if ($request->hasFile('about_image')) {
            if ($landingPage->about_image) {
                Storage::disk('public')->delete($landingPage->about_image);
            }
            $data['about_image'] = $this->compressAndStoreImage($request->file('about_image'), 'landing-pages');
        }

        $landingPage->update($data);

        return redirect()->back()->with('success', 'Landing page berhasil disimpan.');
    }

    /**
     * Compress and store uploaded image
     */
    private function compressAndStoreImage($image, $folder)
    {
        $filename = uniqid() . '_' . time() . '.webp';
        $path = $folder . '/' . $filename;
        
        // Get image info
        $imageInfo = getimagesize($image->getPathname());
        $mime = $imageInfo['mime'];
        
        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($image->getPathname());
                break;
            case 'image/png':
                $source = imagecreatefrompng($image->getPathname());
                break;
            case 'image/gif':
                $source = imagecreatefromgif($image->getPathname());
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($image->getPathname());
                break;
            default:
                // Fallback: just store the original
                return $image->store($folder, 'public');
        }
        
        // Get original dimensions
        $width = imagesx($source);
        $height = imagesy($source);
        
        // Calculate new dimensions (max 1920px width, maintain aspect ratio)
        $maxWidth = 1920;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
        
        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($mime === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save as WebP with 80% quality (good balance between size and quality)
        $tempPath = storage_path('app/public/' . $path);
        
        // Ensure directory exists
        $directory = dirname($tempPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        imagewebp($resized, $tempPath, 80);
        
        // Clean up
        imagedestroy($source);
        imagedestroy($resized);
        
        return $path;
    }

    private function formatNumber($num)
    {
        if ($num > 1000) {
            return floor($num / 1000) . 'k+';
        } elseif ($num > 100) {
            return floor($num / 50) * 50 . '+';
        } elseif ($num > 10) {
            return floor($num / 10) * 10 . '+';
        }
        return $num;
    }

    public function toggleStatus($id)
    {
        $outlet = Outlet::findOrFail($id);
        $landingPage = $outlet->landingPage;

        if ($landingPage) {
            $landingPage->is_active = !$landingPage->is_active;
            $landingPage->save();
            return redirect()->back()->with('success', 'Status landing page berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Landing page belum dikonfigurasi.');
    }
}
