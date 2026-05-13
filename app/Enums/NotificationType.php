<?php

namespace App\Enums;

enum NotificationType: string
{
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}