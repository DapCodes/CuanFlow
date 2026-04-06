<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileCashFlow extends Model
{
    protected $table = 'mobile_cash_flow';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'note',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
