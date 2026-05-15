<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Classroom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s'; // PSR-4 & Filament v3 strict typing

    protected function getStats(): array
    {
        $activeStudents = Student::where('status', 'active')->count();
        $newStudentsThisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
         $activeClasses = Classroom::whereHas('academic_year', fn($q) => $q->where('is_active', true))->count();

        return [
            Stat::make('Total Active Students', number_format($activeStudents))
                ->description('Currently enrolled')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'), // Emerald

            Stat::make('New Enrollments', number_format($newStudentsThisMonth))
                ->description('Joined this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Active Classrooms', number_format($activeClasses))
                ->description('Operating this semester')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning'), // Amber
        ];
    }
}
