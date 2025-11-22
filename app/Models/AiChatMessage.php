<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['ai_chat_session_id', 'role', 'content', 'metadata', 'tokens_used'];
    protected $casts = ['metadata' => 'array'];

    public function session(): BelongsTo { return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id'); }
}