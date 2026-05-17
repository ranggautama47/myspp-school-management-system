<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'amount',
        'expense_date',
        'notes',
        'receipt',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    // =========================================
    // BOOTED
    // =========================================

    protected static function booted(): void
    {
        static::creating(function (self $expense) {
            // Auto-fill recorded_by dari user yang sedang login
            if (empty($expense->recorded_by)) {
                $expense->recorded_by = auth()->id();
            }
        });
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('expense_date', now()->year);
    }

    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }
}