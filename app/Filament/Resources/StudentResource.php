<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Academic';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'nis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Account & Identity')
                    ->description('Link to user account and basic identity.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('email')->email()->required(),
                                Forms\Components\TextInput::make('password')->password()->required(),
                            ]),
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS (Nomor Induk Siswa)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->maxDate(now()),
                    ])->columns(2),

                Forms\Components\Section::make('Academic Placement')
                    ->description('Select classroom. Department and Academic Year will be auto-inherited.')
                    ->schema([
                        Forms\Components\Select::make('classroom_id')
                            ->label('Classroom')
                            ->relationship(
                                name: 'classroom',
                                titleAttribute: 'name',
                                // Hanya tampilkan kelas dari Tahun Ajaran yang Aktif
                                modifyQueryUsing: fn(Builder $query) => $query->whereHas('academicYear', fn($q) => $q->where('is_active', true))
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live() // Memicu re-render UI
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if (!$state) return;
                                $classroom = Classroom::with(['department', 'academicYear'])->find($state);
                                if ($classroom) {
                                    $set('department_hint', $classroom->department->name ?? '-');
                                    $set('academic_year_hint', $classroom->academicYear->name ?? '-');
                                }
                            }),

                        // Dummy placeholders to show operator what will be saved in backend
                        Forms\Components\Placeholder::make('department_hint')
                            ->label('Inherited Department')
                            ->content(fn(Forms\Get $get) => $get('department_hint') ?? 'Will be auto-filled by system.'),

                        Forms\Components\Placeholder::make('academic_year_hint')
                            ->label('Inherited Academic Year')
                            ->content(fn(Forms\Get $get) => $get('academic_year_hint') ?? 'Will be auto-filled by system.'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'graduated' => 'Graduated',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Contact & Guardian Info')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('parent_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('parent_phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Anti N+1 Query: Eager load semua relasi yang ditampilkan
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['user', 'classroom', 'department', 'academicYear']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Class')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success', // Emerald
                        'graduated' => 'info', // Blue
                        'inactive' => 'danger', // Rose
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('parent_phone')
                    ->label('Guardian Contact')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->label('Filter by Class')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Filter by Department'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'graduated' => 'Graduated',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Bisa tambahkan PaymentRelationManager nanti
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
