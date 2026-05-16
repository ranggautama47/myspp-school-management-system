<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Operational = 'operational';
    case Maintenance = 'maintenance';
    case Equipment = 'equipment';
    case Utilities = 'utilities';
    case Salary = 'salary';
    case Event = 'event';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Operational => 'Operasional',
            self::Maintenance => 'Pemeliharaan',
            self::Equipment => 'Peralatan',
            self::Utilities => 'Utilitas',
            self::Salary => 'Gaji/Honor',
            self::Event => 'Kegiatan',
            self::Other => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Operational => 'info',
            self::Maintenance => 'warning',
            self::Equipment => 'primary',
            self::Utilities => 'gray',
            self::Salary => 'success',
            self::Event => 'danger',
            self::Other => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($s) => [$s->value => $s->label()])
            ->toArray();
    }
}