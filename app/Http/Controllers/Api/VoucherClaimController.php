<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherClaimController extends Controller
{
    /**
     * Claim a public discount voucher.
     */
    public function claim(Request $request)
    {
        $request->validate([
            'discount_id' => 'required|exists:discounts,id',
        ]);

        $customer = Customer::where('email', $request->user()->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Profil pelanggan tidak ditemukan.',
            ], 404);
        }

        $discount = Discount::with('outlet')->findOrFail($request->discount_id);

        // Validasi apakah diskon ini publik dan aktif sebagai voucher
        if (!$discount->is_public || !$discount->is_voucher || !$discount->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini tidak tersedia untuk diklaim.',
            ], 400);
        }

        // Cek apakah sudah pernah claim voucher ini
        $alreadyClaimed = CustomerDiscount::where('customer_id', $customer->id)
            ->where('discount_id', $discount->id)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengklaim voucher ini.',
            ], 400);
        }

        // Cek limit penggunaan (optional, tapi disarankan)
        if ($discount->usage_limit > 0 && $discount->used_count >= $discount->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini sudah habis.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $claimedVoucher = CustomerDiscount::create([
                'customer_id' => $customer->id,
                'discount_id' => $discount->id,
                'secret_code' => CustomerDiscount::generateSecretCode($discount->outlet),
                'is_used' => false,
            ]);

            $discount->incrementUsage();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diklaim!',
                'data' => [
                    'id' => $claimedVoucher->id,
                    'secret_code' => $claimedVoucher->secret_code,
                    'discount_name' => $discount->name,
                    'outlet_name' => $discount->outlet->name,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengklaim voucher: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all vouchers claimed by the authenticated customer.
     */
    public function myVouchers(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Profil pelanggan tidak ditemukan.',
            ], 404);
        }

        $vouchers = CustomerDiscount::where('customer_id', $customer->id)
            ->with(['discount.outlet'])
            ->latest()
            ->get()
            ->map(function ($claimed) {
                return [
                    'id' => $claimed->id,
                    'secret_code' => $claimed->secret_code,
                    'is_used' => $claimed->is_used,
                    'used_at' => $claimed->used_at ? $claimed->used_at->format('Y-m-d H:i:s') : null,
                    'discount' => [
                        'id' => $claimed->discount->id,
                        'name' => $claimed->discount->name,
                        'type' => $claimed->discount->type,
                        'value' => $claimed->discount->value,
                        'outlet' => [
                            'id' => $claimed->discount->outlet->id,
                            'name' => $claimed->discount->outlet->name,
                        ]
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vouchers,
        ]);
    }

    /**
     * List all available public vouchers across all outlets.
     */
// In VoucherClaimController.php
public function availableVouchers()
{
    $vouchers = Discount::with('outlet')
        ->where('is_public', true)
        ->where('is_voucher', true)
        ->active()
        ->latest()
        ->get()
        ->map(function ($discount) {
            return [
                'id' => $discount->id,
                'name' => $discount->name,
                'code' => $discount->code,
                'type' => $discount->type,
                'value' => $discount->value,
                'min_purchase' => $discount->min_purchase,
                'max_discount' => $discount->max_discount,
                'is_public' => $discount->is_public, // Add this
                'is_voucher' => $discount->is_voucher, // Add this
                'end_date' => $discount->end_date ? $discount->end_date->format('Y-m-d H:i:s') : null,
                'outlet' => [
                    'id' => $discount->outlet->id,
                    'name' => $discount->outlet->name,
                ],
                'outlet_id' => $discount->outlet_id, // Add this for easier filtering
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $vouchers,
    ]);
}

}
