<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'name',
        'role',
        'content',
        'rating',
        'image',
        'is_published',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
