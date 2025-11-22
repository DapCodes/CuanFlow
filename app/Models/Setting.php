<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['outlet_id', 'group', 'key', 'value', 'type', 'description'];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public function getTypedValue()
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            'float' => (float) $this->value,
            default => $this->value,
        };
    }

    public static function getValue(string $group, string $key, $default = null, ?int $outletId = null)
    {
        $cacheKey = "setting:{$outletId}:{$group}:{$key}";
        return Cache::remember($cacheKey, 3600, function () use ($group, $key, $default, $outletId) {
            $s = static::where('group', $group)->where('key', $key)->where('outlet_id', $outletId)->first();
            if (!$s && $outletId) $s = static::where('group', $group)->where('key', $key)->whereNull('outlet_id')->first();
            return $s ? $s->getTypedValue() : $default;
        });
    }

    public static function setValue(string $group, string $key, $value, string $type = 'string', ?int $outletId = null): self
    {
        $s = static::updateOrCreate(
            ['group' => $group, 'key' => $key, 'outlet_id' => $outletId],
            ['value' => is_array($value) ? json_encode($value) : $value, 'type' => $type]
        );
        Cache::forget("setting:{$outletId}:{$group}:{$key}");
        return $s;
    }

    public static function getGroup(string $group, ?int $outletId = null): array
    {
        return static::where('group', $group)
            ->where(fn($q) => $q->where('outlet_id', $outletId)->orWhereNull('outlet_id'))
            ->get()->mapWithKeys(fn($s) => [$s->key => $s->getTypedValue()])->toArray();
    }
}