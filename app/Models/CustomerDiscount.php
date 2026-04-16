<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'discount_id',
        'secret_code',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Generate a unique secret code for the voucher.
     * Format: XXX-123456 (Outlet Initials - Random Alphanumeric)
     */
    public static function generateSecretCode(Outlet $outlet): string
    {
        $initials = self::getOutletInitials($outlet->name);

        do {
            $random = strtoupper(Str::random(6));
            $code = "{$initials}-{$random}";
        } while (self::where('secret_code', $code)->exists());

        return $code;
    }

    private static function getOutletInitials(string $name): string
    {
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            $initials = '';
            foreach ($words as $w) {
                $initials .= strtoupper(substr($w, 0, 1));
            }

            return substr($initials, 0, 3);
        }

        return strtoupper(substr($name, 0, 3));
    }
}
