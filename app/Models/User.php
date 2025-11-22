<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, CausesActivity, LogsActivity;

    protected $fillable = [
        'name', 'email', 'password', 'outlet_id', 'phone', 'avatar', 'is_active', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'email', 'outlet_id', 'is_active'])->logOnlyDirty();
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function sales(): HasMany { return $this->hasMany(Sale::class, 'cashier_id'); }
    public function cashRegisters(): HasMany { return $this->hasMany(CashRegister::class); }
    public function aiChatSessions(): HasMany { return $this->hasMany(AiChatSession::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByOutlet($q, $outletId) { return $q->where('outlet_id', $outletId); }

    public function isOwner(): bool { return $this->hasRole('owner'); }
    public function canAccessOutlet($outletId): bool { return $this->isOwner() || $this->outlet_id == $outletId; }
}