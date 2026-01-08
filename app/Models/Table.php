<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'outlet_id',
        'table_number',
        'code',
        'name',
        'capacity',
        'location',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Tersedia',
            self::STATUS_OCCUPIED => 'Terisi',
            self::STATUS_RESERVED => 'Dipesan',
            self::STATUS_MAINTENANCE => 'Maintenance',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_AVAILABLE => 'emerald',
            self::STATUS_OCCUPIED => 'red',
            self::STATUS_RESERVED => 'yellow',
            self::STATUS_MAINTENANCE => 'gray',
        ];
    }

    // Relationships
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function getDisplayName(): string
    {
        return $this->name ?: 'Meja ' . $this->table_number;
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    // Actions
    public function markAsOccupied(): bool
    {
        $this->status = self::STATUS_OCCUPIED;
        return $this->save();
    }

    public function markAsAvailable(): bool
    {
        $this->status = self::STATUS_AVAILABLE;
        return $this->save();
    }

    public function markAsReserved(): bool
    {
        $this->status = self::STATUS_RESERVED;
        return $this->save();
    }
}
