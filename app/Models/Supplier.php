<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'name', 'contact_person', 'phone', 'email', 'address', 'notes', 'is_active', 'outlet_id'];

    protected $casts = ['is_active' => 'boolean'];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getWhatsappUrlAttribute()
    {
        if (empty($this->phone)) {
            return null;
        }

        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $this->phone);

        // Check for empty after cleanup
        if (empty($number)) {
            return null;
        }

        // If starts with '0', replace with '62'
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }
        // If it doesn't start with '62' at this point, prepend '62'
        elseif (! str_starts_with($number, '62')) {
            $number = '62'.$number;
        }

        return "https://wa.me/{$number}";
    }
}
