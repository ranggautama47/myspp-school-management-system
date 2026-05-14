<?php
namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Student')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->badge(fn() => \App\Models\User::count()),

            'students' => Tab::make('Students')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereIn('users.id', function ($q) {
                        $q->select('model_id')
                          ->from('model_has_roles')
                          ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                          ->where('roles.name', 'student')
                          ->where('model_has_roles.model_type', 'App\Models\User');
                    });
                })
                ->badge(fn() => \App\Models\User::students()->count())
                ->badgeColor('info'),

            'admins' => Tab::make('Admins')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereIn('users.id', function ($q) {
                        $q->select('model_id')
                          ->from('model_has_roles')
                          ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                          ->where('roles.name', 'admin')
                          ->where('model_has_roles.model_type', 'App\Models\User');
                    });
                })
                ->badge(fn() => \App\Models\User::admins()->count())
                ->badgeColor('warning'),
        ];
    }
}