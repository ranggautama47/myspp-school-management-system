<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case GoPay        = 'gopay';
    case Ovo          = 'ovo';
    case Qris         = 'qris';
    case BankTransfer = 'bank_transfer';
    case CreditCard   = 'credit_card';
    case Manual       = 'manual'; // upload bukti bayar manual

    public function label(): string
    {
        return match($this) {
            self::GoPay        => 'GoPay',
            self::Ovo          => 'OVO',
            self::Qris         => 'QRIS',
            self::BankTransfer => 'Transfer Bank (VA)',
            self::CreditCard   => 'Kartu Kredit',
            self::Manual       => 'Manual / Upload Bukti',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($m) => [$m->value => $m->label()])
            ->toArray();
    }
}
