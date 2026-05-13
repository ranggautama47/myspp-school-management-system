<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'status',
        'raw_response',
        'midtrans_order_id',
    ];

    protected function casts(): array
    {
        return [
            'raw_response' => 'array', // JSON otomatis jadi array
        ];
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // =========================================
    // STATIC HELPER — simpan log dari webhook
    // Dipanggil dari MidtransService@handleWebhook
    // =========================================

    public static function record(Transaction $transaction, array $response): self
    {
        return self::create([
            'transaction_id'    => $transaction->id,
            'status'            => $response['transaction_status'] ?? 'unknown',
            'raw_response'      => $response,
            'midtrans_order_id' => $response['order_id'] ?? $transaction->code,
        ]);
    }

    // =========================================
    // STATUS HELPERS
    // Sesuai pemetaan status di payment-flow.md
    // =========================================

    public function isSettlement(): bool
    {
        return $this->status === 'settlement';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expire';
    }

    public function isDeny(): bool
    {
        return $this->status === 'deny';
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getPaymentTypeAttribute(): ?string
    {
        return $this->raw_response['payment_type'] ?? null;
    }

    /**
     * Fix: return float bukan string.
     * Midtrans kirim gross_amount sebagai string "25000.00"
     */
    public function getGrossAmountAttribute(): ?float
    {
        return isset($this->raw_response['gross_amount'])
            ? (float) $this->raw_response['gross_amount']
            : null;
    }

    public function getResponseValue(string $key): mixed
    {
        return $this->raw_response[$key] ?? null;
    }
}
