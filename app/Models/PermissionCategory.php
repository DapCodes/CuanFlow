<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

class PermissionCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
    ];

    /**
     * Get all permissions in this category.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'permission_category_id');
    }

    /**
     * Scope untuk mengurutkan berdasarkan order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
