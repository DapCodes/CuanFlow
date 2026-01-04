<?php

namespace App\Http\Controllers;

use App\Models\OutletPaymentLink;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OutletPaymentLinkController extends Controller
{
    public function index()
    {
        $outletId = auth()->user()->outlet_id;
        
        $paymentLinks = OutletPaymentLink::with('paymentMethod')
            ->where('outlet_id', $outletId)
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => OutletPaymentLink::where('outlet_id', $outletId)->count(),
            'active' => OutletPaymentLink::where('outlet_id', $outletId)->where('is_active', true)->count(),
            'inactive' => OutletPaymentLink::where('outlet_id', $outletId)->where('is_active', false)->count(),
        ];

        return view('main.outlet-payment-links.index', compact('paymentLinks', 'stats'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::active()->get();
        $outletId = auth()->user()->outlet_id;
        
        // Get payment methods yang sudah digunakan
        $usedMethodIds = OutletPaymentLink::where('outlet_id', $outletId)
            ->pluck('payment_method_id')
            ->toArray();
        
        return view('main.outlet-payment-links.create', compact('paymentMethods', 'usedMethodIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'qr_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'payment_method_id.required' => 'Metode pembayaran wajib dipilih',
            'payment_method_id.exists' => 'Metode pembayaran tidak valid',
            'qr_image.image' => 'File harus berupa gambar',
            'qr_image.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'qr_image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $outletId = auth()->user()->outlet_id;

        // Check duplicate
        $exists = OutletPaymentLink::where('outlet_id', $outletId)
            ->where('payment_method_id', $validated['payment_method_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Metode pembayaran ini sudah ditambahkan sebelumnya')->withInput();
        }

        $data = $validated;
        $data['outlet_id'] = $outletId;
        $data['is_active'] = $request->has('is_active');

        // Upload QR Image
        if ($request->hasFile('qr_image')) {
            $file = $request->file('qr_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('qr', $filename, 'public');
            $data['qr_image'] = $path;
        }

        OutletPaymentLink::create($data);

        return redirect()->route('outlet-payment-links.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function show(OutletPaymentLink $outletPaymentLink)
    {
        // Validasi outlet_id
        if ($outletPaymentLink->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda tidak memiliki akses ke metode pembayaran ini');
        }
        
        $outletPaymentLink->load('paymentMethod', 'outlet');
        
        return view('main.outlet-payment-links.show', compact('outletPaymentLink'));
    }

    public function edit(OutletPaymentLink $outletPaymentLink)
    {
        // Validasi outlet_id
        if ($outletPaymentLink->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda tidak memiliki akses ke metode pembayaran ini');
        }
        
        return view('main.outlet-payment-links.edit', compact('outletPaymentLink'));
    }

    public function update(Request $request, OutletPaymentLink $outletPaymentLink)
    {
        // Validasi outlet_id
        if ($outletPaymentLink->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda tidak memiliki akses ke metode pembayaran ini');
        }

        $validated = $request->validate([
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'qr_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'qr_image.image' => 'File harus berupa gambar',
            'qr_image.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'qr_image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $data = $validated;
        $data['is_active'] = $request->has('is_active');

        // Upload QR Image baru
        if ($request->hasFile('qr_image')) {
            // Hapus gambar lama
            if ($outletPaymentLink->qr_image) {
                Storage::disk('public')->delete($outletPaymentLink->qr_image);
            }

            $file = $request->file('qr_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('qr', $filename, 'public');
            $data['qr_image'] = $path;
        }

        $outletPaymentLink->update($data);

        return redirect()->route('outlet-payment-links.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function destroy(OutletPaymentLink $outletPaymentLink)
    {
        // Validasi outlet_id
        if ($outletPaymentLink->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda tidak memiliki akses ke metode pembayaran ini');
        }

        // Hapus gambar QR jika ada
        if ($outletPaymentLink->qr_image) {
            Storage::disk('public')->delete($outletPaymentLink->qr_image);
        }

        $outletPaymentLink->delete();

        return redirect()->route('outlet-payment-links.index')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }

    public function toggleStatus(OutletPaymentLink $outletPaymentLink)
    {
        // Validasi outlet_id
        if ($outletPaymentLink->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda tidak memiliki akses ke metode pembayaran ini');
        }

        $outletPaymentLink->update([
            'is_active' => !$outletPaymentLink->is_active
        ]);

        $status = $outletPaymentLink->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Metode pembayaran berhasil {$status}");
    }
}