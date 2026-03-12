<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResellerApplicationController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('lihat reseller applications')) {
            abort(403);
        }

        $query = \App\Models\ResellerApplication::query();
        
        // Stats
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $applications = $query->with(['customer', 'outlet'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('main.reseller-applications.index', compact('applications', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'description' => 'required|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('reseller_docs', 'public');
        }

        \App\Models\ResellerApplication::create([
            'customer_id' => $request->customer_id,
            'outlet_id' => $request->outlet_id,
            'description' => $request->description,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Lamaran reseller berhasil dikirim.');
    }

    public function update(Request $request, \App\Models\ResellerApplication $reseller_application)
    {
        if (! auth()->user()->can('kelola reseller applications')) {
            abort(403);
        }

        $application = $reseller_application;

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application->update([
            'status' => $request->status,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Update customer type instead of user role
        $customer = $application->customer;
        if ($customer) {
            if ($request->status === 'approved') {
                // Change customer type to reseller
                $customer->update(['type' => 'reseller']);
            } else {
                // If rejected and was reseller, revert to regular
                if ($customer->type === 'reseller') {
                    $customer->update(['type' => 'regular']);
                }
            }
        }

        return back()->with('success', 'Status lamaran diperbarui.');
    }

    public function toggleAcceptance(Request $request)
    {
        $user = auth()->user();
        $outlet = $user->outlet;

        if (! $outlet) {
            return response()->json(['message' => 'Outlet tidak ditemukan.'], 404);
        }

        if (! $user->can('kelola reseller applications') && ! $user->isOwner()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $outlet->update([
            'accepts_reseller' => ! $outlet->accepts_reseller,
        ]);

        return response()->json([
            'success' => true,
            'accepts_reseller' => $outlet->accepts_reseller,
            'message' => $outlet->accepts_reseller ? 'Outlet sekarang menerima lamaran reseller.' : 'Outlet berhenti menerima lamaran reseller.',
        ]);
    }
}
