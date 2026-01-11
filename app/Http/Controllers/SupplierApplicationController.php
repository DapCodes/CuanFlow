<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierApplicationController extends Controller
{
    public function index()
    {
        $applications = \App\Models\SupplierApplication::with(['user', 'outlet'])
            ->latest()
            ->paginate(10);
            
        return view('main.supplier-applications.index', compact('applications'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'description' => 'required|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('supplier_docs', 'public');
        }

        \App\Models\SupplierApplication::create([
            'user_id' => auth()->id(),
            'outlet_id' => $request->outlet_id,
            'description' => $request->description,
            'document_path' => $path,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Lamaran supplier berhasil dikirim.');
    }

    public function update(Request $request, \App\Models\SupplierApplication $supplier_application)
    {
        // Add permission check if needed
        $application = $supplier_application; // Alias for minimal code change or just rename variables below
        
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $application->update([
            'status' => $request->status,
            'processed_by' => auth()->id(),
            'processed_at' => now()
        ]);

        $user = $application->user;
        if ($user) {
            if ($request->status === 'approved') {
                // Remove 'pelanggan' if exists, add 'supplier'
                // syncRoles replaces ALL roles with the given ones.
                // If user should ONLY be supplier:
                $user->syncRoles('supplier');
            } else {
                // If rejected, ensure they are not supplier.
                if ($user->hasRole('supplier')) {
                    $user->removeRole('supplier');
                    // Ensure they have fallback role
                    if (!$user->hasRole('pelanggan')) {
                        $user->assignRole('pelanggan');
                    }
                }
            }
        }

        return back()->with('success', 'Status lamaran diperbarui.');
    }
}
