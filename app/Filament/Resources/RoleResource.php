<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 2;

    protected static ?string $recordTitleAttribute = 'name';

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        // Kelompokkan permissions per modul untuk UI lebih rapi
        $groupedPermissions = Permission::all()
            ->groupBy(fn($p) => match (true) {
                str_contains($p->name, 'department')  => 'Academic',
                str_contains($p->name, 'classroom')   => 'Academic',
                str_contains($p->name, 'student')     => 'Academic',
                str_contains($p->name, 'academic')    => 'Academic',
                str_contains($p->name, 'transaction')  => 'Finance',
                str_contains($p->name, 'payment')     => 'Finance',
                str_contains($p->name, 'report')      => 'Finance',
                str_contains($p->name, 'export')      => 'Finance',
                str_contains($p->name, 'user')        => 'System',
                str_contains($p->name, 'role')        => 'System',
                str_contains($p->name, 'setting')     => 'System',
                str_contains($p->name, 'audit')       => 'System',
                default                               => 'Other',
            });

        return $form->schema([
            Forms\Components\Section::make('Role Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText('Gunakan lowercase dengan spasi. Contoh: "bendahara", "operator"')
                        ->disabled(fn($record) => $record?->name === 'super admin'),

                    Forms\Components\TextInput::make('guard_name')
                        ->label('Guard')
                        ->default('web')
                        ->disabled()
                        ->helperText('Default: web'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Permissions')
                ->description('Assign permissions untuk role ini. Super Admin otomatis mendapat semua permission.')
                ->schema(
                    $groupedPermissions->map(function ($permissions, $group) {
                        return Forms\Components\Fieldset::make($group)
                            ->schema([
                                Forms\Components\CheckboxList::make('permissions')
                                    ->label('')
                                    ->relationship('permissions', 'name')
                                    ->options(
                                        $permissions->pluck('name', 'id')
                                            ->map(fn($name) => ucwords($name))
                                            ->toArray()
                                    )
                                    ->columns(2)
                                    ->gridDirection('row'),
                            ]);
                    })->values()->toArray()
                ),
        ]);
    }

    // =========================================
    // TABLE
    // =========================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role Name')
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'super admin' => 'danger',
                        'admin'       => 'warning',
                        'operator'    => 'info',
                        'bendahara'   => 'success',
                        'student'     => 'gray',
                        default       => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->name === 'super admin'),

                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => $record->name === 'super admin')
                    ->before(function ($record, Tables\Actions\DeleteAction $action) {
                        if ($record->users()->count() > 0) {
                            $action->cancel();
                            \Filament\Notifications\Notification::make()
                                ->title('Tidak bisa dihapus')
                                ->body("Role [{$record->name}] masih memiliki " . $record->users()->count() . " user aktif.")
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'asc');
    }

    // =========================================
    // QUERY — eager loading hindari N+1
    // =========================================

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['permissions', 'users']);
    }

    // =========================================
    // PAGES
    // =========================================

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'gray';
    }
}
