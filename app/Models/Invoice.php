<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'student_id',
        'department_id',
        'amount',
        'due_date',
        'status',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    // =========================================
    // BOOTED
    // =========================================

    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (empty($invoice->number)) {
                $invoice->number = self::generateNumber();
            }
        });

        // Auto overdue: saat diambil, cek due_date
        static::retrieved(function (self $invoice) {
            if (
                $invoice->status === InvoiceStatus::Unpaid &&
                $invoice->due_date->isPast()
            ) {
                // Update tanpa trigger event lagi
                $invoice->updateQuietly(['status' => InvoiceStatus::Overdue]);
            }
        });
    }

    public static function generateNumber(): string
    {
        for ($i = 0; $i < 5; $i++) {
            $number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            if (!self::withTrashed()->where('number', $number)->exists()) {
                return $number;
            }
        }

        throw new \RuntimeException('Gagal generate nomor invoice unik setelah 5 percobaan.');
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeUnpaid($query)
    {
        return $query->where('status', InvoiceStatus::Unpaid);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::Paid);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::Overdue);
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year);
    }

    // =========================================
    // HELPERS
    // =========================================

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::Paid;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::Overdue ||
            ($this->status === InvoiceStatus::Unpaid && $this->due_date->isPast());
    }

    public function canBePaid(): bool
    {
        return $this->status->canBePaid();
    }

    public function markAsPaid(Transaction $transaction): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->update([
            'status' => InvoiceStatus::Paid,
            'transaction_id' => $transaction->id,
        ]);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }
}