<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'department_id',
        'amount',
        'payment_method',
        'payment_status',
        'snap_token',
        'midtrans_url',
        'proof_of_payment',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_status' => TransactionStatus::class,
            'paid_at'        => 'datetime',
            'deleted_at'     => 'datetime',
        ];
    }

    // =========================================
    // BOOTED — lebih modern dari boot()
    // =========================================

    protected static function booted(): void
    {
        static::creating(function (self $transaction) {
            if (empty($transaction->code)) {
                $transaction->code = self::generateCode();
            }
        });
    }

    /**
     * Generate kode unik: TRX-20250511-A7K2M
     *
     * Throw RuntimeException jika 5x percobaan masih gagal.
     * (Fix dari versi lama yang bisa return duplicate)
     */
    public static function generateCode(): string
    {
        for ($i = 0; $i < 5; $i++) {
            $code = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            if (! self::withTrashed()->where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Gagal generate kode transaksi unik setelah 5 percobaan.');
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopePending($query)
    {
        return $query->where('payment_status', TransactionStatus::Pending);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', TransactionStatus::Paid);
    }

    public function scopeExpired($query)
    {
        return $query->where('payment_status', TransactionStatus::Expired);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year);
    }

    // =========================================
    // STATUS HELPERS
    // =========================================

    public function isPending(): bool
    {
        return $this->payment_status === TransactionStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === TransactionStatus::Paid;
    }

    public function canBePaid(): bool
    {
        return $this->payment_status->canBePaid();
    }

    /**
     * Tandai transaksi sebagai lunas.
     * Dipanggil dari MidtransService saat webhook settlement/capture.
     *
     * Guard: return early jika sudah paid — mencegah overwrite.
     */
    public function markAsPaid(string $paymentMethod): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->update([
            'payment_status' => TransactionStatus::Paid,
            'payment_method' => $paymentMethod,
            'paid_at'        => now(),
        ]);
    }

    /**
     * Tandai transaksi sebagai expired.
     * Dipanggil dari MidtransService saat webhook expire.
     */
    public function markAsExpired(): void
    {
        if ($this->isPaid()) {
            return; // jangan expire transaksi yang sudah lunas
        }

        $this->update([
            'payment_status' => TransactionStatus::Expired,
        ]);
    }
}
