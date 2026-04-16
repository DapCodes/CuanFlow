<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerApplication extends Model
{
    protected $fillable = [
        'customer_id',
        'outlet_id',
        'description',
        'document_path',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
