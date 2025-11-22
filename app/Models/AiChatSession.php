<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'outlet_id', 'title'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function messages(): HasMany { return $this->hasMany(AiChatMessage::class)->orderBy('created_at'); }

    public function addMessage(string $role, string $content, ?array $meta = null): AiChatMessage
    {
        return $this->messages()->create(['role' => $role, 'content' => $content, 'metadata' => $meta]);
    }

    public function getConversationHistory(): array
    {
        return $this->messages->map(fn($m) => ['role' => $m->role, 'content' => $m->content])->toArray();
    }
}