<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutletPolicy extends Model
{
    protected $fillable = [
        'outlet_id',
        'title',
        'content',
        'category',
        'is_active',
        'created_by',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
