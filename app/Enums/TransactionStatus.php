<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Failed    = 'failed';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    // Label untuk UI
    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Menunggu Pembayaran',
            self::Paid      => 'Lunas',
            self::Failed    => 'Gagal',
            self::Expired   => 'Kadaluarsa',
            self::Cancelled => 'Dibatalkan',
        };
    }

    // Warna badge Filament
    public function color(): string
    {
        return match($this) {
            self::Pending   => 'warning',
            self::Paid      => 'success',
            self::Failed    => 'danger',
            self::Expired   => 'gray',
            self::Cancelled => 'gray',
        };
    }

    // Icon Heroicon untuk Filament
    public function icon(): string
    {
        return match($this) {
            self::Pending   => 'heroicon-o-clock',
            self::Paid      => 'heroicon-o-check-circle',
            self::Failed    => 'heroicon-o-x-circle',
            self::Expired   => 'heroicon-o-calendar-x',
            self::Cancelled => 'heroicon-o-trash',
        };
    }

    // Apakah masih bisa dibayar?
    public function canBePaid(): bool
    {
        return $this === self::Pending;
    }

    // Apakah status sudah final (tidak bisa berubah lagi)?
    public function isFinal(): bool
    {
        return in_array($this, [
            self::Paid,
            self::Failed,
            self::Expired,
            self::Cancelled,
        ]);
    }

    // Untuk dropdown di Filament / Form
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($s) => [$s->value => $s->label()])
            ->toArray();
    }
}
