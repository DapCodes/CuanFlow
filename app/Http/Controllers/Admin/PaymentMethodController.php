<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentMethod::withCount('outletPaymentLinks');
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        
        $paymentMethods = $query->orderBy('name')->paginate(15);
        
        return view('admin.master.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.master.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('payment-methods', 'public');
        }

        PaymentMethod::create([
            'name' => $validated['name'],
            'code' => Str::lower($validated['code']),
            'icon' => $iconPath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dibuat.');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $paymentMethod->load(['outletPaymentLinks.outlet']);
        return view('admin.master.payment-methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.master.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:payment_methods,code,' . $paymentMethod->id],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $iconPath = $paymentMethod->icon;
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($paymentMethod->icon && Storage::disk('public')->exists($paymentMethod->icon)) {
                Storage::disk('public')->delete($paymentMethod->icon);
            }
            $iconPath = $request->file('icon')->store('payment-methods', 'public');
        }

        $paymentMethod->update([
            'name' => $validated['name'],
            'code' => Str::lower($validated['code']),
            'icon' => $iconPath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Check if payment method is being used
        if ($paymentMethod->outletPaymentLinks()->count() > 0) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'Metode pembayaran tidak dapat dihapus karena sedang digunakan.');
        }

        // Delete icon
        if ($paymentMethod->icon && Storage::disk('public')->exists($paymentMethod->icon)) {
            Storage::disk('public')->delete($paymentMethod->icon);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Status metode pembayaran berhasil diubah.');
    }
}
