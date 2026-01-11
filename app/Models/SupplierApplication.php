<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierApplication extends Model
{
    protected $fillable = [
        'user_id',
        'outlet_id',
        'description',
        'document_path',
        'status',
        'processed_by',
        'processed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
