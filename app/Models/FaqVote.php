<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_id',
        'user_id',
        'is_helpful',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
