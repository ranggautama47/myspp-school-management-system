<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days'; // Sesuaikan icon
    protected static ?string $navigationGroup = 'Academic'; // Biar rapi satu grup
    protected static ?int $navigationSort = 4;

    // ==========================================================
    // 1. TARUH KODE FORM DI SINI
    // ==========================================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Academic Year Period')
                ->description('Define the start and end of the educational cycle.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->placeholder('e.g. Angkatan Tahun Ajaran 2025/2026')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date')->required(),
                    ]),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Set as Active Year')
                        ->helperText('Activating this will deactivate all other academic years.')
                        ->onColor('success'),
                ])
        ]);
    }

    // ==========================================================
    // 2. TARUH KODE TABLE DI SINI
    // ==========================================================
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->weight('bold'),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
            Tables\Columns\IconColumn::make('is_active')
                ->label('Status')
                ->boolean()
                ->trueColor('success')
                ->falseColor('slate'),
        ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Only Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Daftarkan class yang baru dibuat tadi di sini
            // RelationManagers\ClassroomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
