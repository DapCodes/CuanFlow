<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}
