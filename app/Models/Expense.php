<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'status', 'expense_date', 'description', 'type'])
            ->logOnlyDirty()
            ->useLogName('expense')
            ->setDescriptionForEvent(fn (string $eventName) => "Expense {$this->expense_number} was {$eventName}");
    }

    protected $fillable = [
        'expense_number', 'outlet_id', 'expense_category_id', 'amount',
        'expense_date', 'description', 'receipt_image', 'payment_method',
        'reference_number', 'notes', 'created_by', 'approved_by', 'status', 'type',
    ];

    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $prefix = $m->type === 'income' ? 'INC-' : 'EXP-';
            $m->expense_number = $m->expense_number ?: $prefix.date('Ymd').'-'.str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByOutlet($q, $id)
    {
        return $q->where('outlet_id', $id);
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function scopeThisMonth($q)
    {
        return $q->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year);
    }
}
