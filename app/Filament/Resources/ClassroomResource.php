<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassroomResource\Pages;
use App\Filament\Resources\ClassroomResource\RelationManagers;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Class Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Class Name')
                        ->placeholder('e.g. XII RPL 1')
                        ->required(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                "{$record->name} — Semester {$record->semester}"
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->relationship('academic_year', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                    Forms\Components\TextInput::make('capacity')
                        ->numeric()
                        ->default(36)
                        ->suffix('Students'),
                    Forms\Components\Textarea::make('description')
                        ->placeholder('e.g. info tambahan')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->description(fn($record) => $record->department->name),
            Tables\Columns\TextColumn::make('academic_year.name')
                ->badge()
                ->color('emerald'),
            Tables\Columns\TextColumn::make('capacity')
                ->numeric()
                ->alignCenter(),
            Tables\Columns\TextColumn::make('students_count')
                ->label('Total Students')
                ->counts('students') // Assumes relationship 'students' exists
                ->badge(),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
                Tables\Filters\SelectFilter::make('academic_year')
                    ->relationship('academic_year', 'name'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}
