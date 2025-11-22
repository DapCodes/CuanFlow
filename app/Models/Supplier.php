<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'name', 'contact_person', 'phone', 'email', 'address', 'notes', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function rawMaterials(): HasMany { return $this->hasMany(RawMaterial::class); }
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}