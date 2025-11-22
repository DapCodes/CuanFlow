<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeyboardShortcut extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'shortcut', 'description', 'context', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function getForUser(?int $userId, string $context = 'pos'): array
    {
        return static::where('context', $context)->where('is_active', true)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->orderByRaw('user_id IS NULL')->get()->unique('action')
            ->mapWithKeys(fn($s) => [$s->action => $s->shortcut])->toArray();
    }
}