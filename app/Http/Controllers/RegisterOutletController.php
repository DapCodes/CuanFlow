<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterOutletController extends Controller
{
    public function index()
    {
        // Jika user sudah memiliki outlet, redirect ke dashboard
        if (Auth::user()->outlet_id) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah terdaftar di outlet.');
        }

        return view('main.outlets.register-outlet');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'business_type' => 'required|string|max:100',
            'business_category' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longtitude' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique outlet code
            $code = $this->generateOutletCode();

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('outlets/logos', 'public');
            }

            // Create outlet
            $outlet = Outlet::create([
                'code' => $code,
                'name' => $request->name,
                'owner_id' => Auth::id(),
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longtitude' => $request->longtitude,
                'phone' => $request->phone,
                'email' => $request->email,
                'logo' => $logoPath,
                'settings' => [
                    'business_type' => $request->business_type,
                    'business_category' => $request->business_category,
                    'timezone' => 'Asia/Jakarta',
                    'currency' => 'IDR',
                ],
                'is_active' => true,
            ]);

            // Update user's outlet_id
            $user = Auth::user();
            $user->outlet_id = $outlet->id;
            $user->save();

            // Assign owner role if using Spatie Permission
            if (!$user->hasRole('owner')) {
                $user->assignRole('owner');
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Outlet berhasil didaftarkan! Selamat datang di CuanFlow.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded logo if exists
            if (isset($logoPath) && $logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mendaftarkan outlet: ' . $e->getMessage());
        }
    }

    private function generateOutletCode()
    {
        do {
            $code = 'OUT-' . strtoupper(Str::random(8));
        } while (Outlet::where('code', $code)->exists());

        return $code;
    }
}