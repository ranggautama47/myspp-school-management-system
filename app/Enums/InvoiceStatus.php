<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::Paid => 'Lunas',
            self::Overdue => 'Jatuh Tempo',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'warning',
            self::Paid => 'success',
            self::Overdue => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Unpaid => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Overdue => 'heroicon-o-exclamation-triangle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function canBePaid(): bool
    {
        return in_array($this, [self::Unpaid, self::Overdue]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($s) => [$s->value => $s->label()])
            ->toArray();
    }
}