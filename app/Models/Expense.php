<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_number', 'outlet_id', 'expense_category_id', 'amount',
        'expense_date', 'description', 'receipt_image', 'payment_method',
        'reference_number', 'notes', 'created_by', 'approved_by', 'status'
    ];

    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->expense_number = $m->expense_number ?: 'EXP-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT));
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopeThisMonth($q) { return $q->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year); }
}