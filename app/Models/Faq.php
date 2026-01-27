<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'type',
        'priority',
        'is_active',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'order' => 'integer',
    ];

    public function votes()
    {
        return $this->hasMany(FaqVote::class);
    }

    public function currentUserVote()
    {
        return $this->hasOne(FaqVote::class)->where('user_id', auth()->id());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }

        return $query;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public static function getTypes()
    {
        return [
            'general' => 'Umum',
            'pos' => 'Point of Sale',
            'product' => 'Produk & Stok',
            'finance' => 'Keuangan',
            'report' => 'Laporan',
            'account' => 'Akun & Pengaturan',
        ];
    }

    public static function getPriorities()
    {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
        ];
    }

    public function getTypeLabel()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getPriorityLabel()
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }
}
