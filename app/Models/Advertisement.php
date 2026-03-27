<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'banner',
        'content',
        'url',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = [
        'banner_url',
    ];

    public function getBannerUrlAttribute()
    {
        if ($this->banner) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->banner);
        }
        return asset('assets/image/placeholder-image.png');
    }
}
