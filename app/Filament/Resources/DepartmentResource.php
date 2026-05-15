<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Departments';
    protected static ?string $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 2;

    // Global search
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Department Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Department Name')
                        ->placeholder('e.g. Teknik Informatika')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('semester')
                        ->label('Semester')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(12)
                        ->required()
                        ->suffix('semester'),

                    Forms\Components\TextInput::make('cost')
                        ->label('SPP Cost (IDR)')
                        ->prefix('Rp')
                        ->placeholder('2.500.000')
                        ->required()
                        /** * 1. LOGIKA DINAMIS:
                         * Menggunakan Alpine.js Mask bawaan Filament. 
                         * Menghasilkan format 1.000, 10.000, 100.000 secara otomatis saat diketik.
                         */
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$money($input, ".")',
                            'inputmode' => 'numeric', // Memaksa keyboard angka muncul di HP
                        ])
                        /**
                         * 2. KEAMANAN DATA:
                         * 'stripCharacters' akan membuang semua titik sebelum data dikirim ke Database.
                         * Jadi di DB tetap tersimpan sebagai integer (contoh: 2500000).
                         */
                        ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state))
                ])
                ->columns(2),
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
                    ->label('Department Name')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('SPP Cost')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Total Students')
                    ->counts('students')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('semester')
                    ->label('Semester')
                    ->options(
                        collect(range(1, 12))
                            ->mapWithKeys(fn($s) => [$s => "Semester {$s}"])
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->striped();
    }

    // =========================================
    // SOFT DELETE QUERY
    // =========================================

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    // =========================================
    // PAGES
    // =========================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    // Badge jumlah di navigation
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
