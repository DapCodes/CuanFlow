<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'type',
        'subject',
        'content',
        'target_role',
        'target_user_ids',
        'total_recipients',
    ];

    protected $casts = [
        'target_user_ids' => 'json',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
