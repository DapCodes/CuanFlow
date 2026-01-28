<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerDebtResource;
use App\Http\Resources\SaleResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    /**
     * Get purchase history for the authenticated customer.
     */
    public function purchases(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer profile not found.',
                'data' => []
            ], 404);
        }

        $purchases = $customer->sales()
            ->with(['items', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 15));

        return SaleResource::collection($purchases);
    }

    /**
     * Get debt list for the authenticated customer.
     */
    public function debts(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer profile not found.',
                'data' => []
            ], 404);
        }

        $debts = $customer->debts()
            ->with(['sale', 'outlet'])
            ->orderBy('due_date', 'asc')
            ->get();

        return CustomerDebtResource::collection($debts);
    }
}
