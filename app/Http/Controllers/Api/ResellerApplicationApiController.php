<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResellerApplicationResource;
use App\Models\Customer;
use App\Models\ResellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResellerApplicationApiController extends Controller
{
    /**
     * Display a listing of personal reseller applications.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No customer record found for this account.'
            ]);
        }

        $applications = ResellerApplication::with('outlet')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return ResellerApplicationResource::collection($applications);
    }

    /**
     * Store a new reseller application.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = Customer::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'phone' => $user->phone,
                'type' => 'regular',
                'is_active' => true,
            ]
        );

        // Check if there is already a pending application for this outlet
        $existing = ResellerApplication::where('customer_id', $customer->id)
            ->where('outlet_id', $request->outlet_id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah memiliki lamaran yang sedang diproses untuk outlet ini.',
            ], 422);
        }

        // Check if outlet actually accepts reseller applications
        $outlet = \App\Models\Outlet::find($request->outlet_id);
        if (!$outlet || !$outlet->accepts_reseller) {
            return response()->json([
                'message' => 'Outlet ini sedang tidak menerima lamaran reseller.',
            ], 422);
        }

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'description' => 'required|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('reseller_docs', 'public');
        }

        $application = ResellerApplication::create([
            'customer_id' => $customer->id,
            'outlet_id' => $request->outlet_id,
            'description' => $request->description,
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Lamaran reseller berhasil dikirim.',
            'data' => new ResellerApplicationResource($application->load('outlet')),
        ], 201);
    }

    /**
     * Display the specified reseller application.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        $application = ResellerApplication::with('outlet')
            ->where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        return new ResellerApplicationResource($application);
    }
}
