<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Classroom;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Account & Identity')
                    ->description('Link to user account and basic identity.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship(
                                'user',
                                'name',
                                // Menampilkan hanya user yang belum memiliki record di tabel students
                                modifyQueryUsing: function ($query, $operation, $record) {
                                    // Jika sedang VIEW atau EDIT, kita harus pastikan user yang sudah terpilih tetap muncul
                                    if ($operation === 'view' || $operation === 'edit') {
                                        return $query;
                                    }

                                    // Jika sedang CREATE, baru kita filter hanya student yang belum punya record
                                    return $query->whereHas('roles', fn($q) => $q->where('name', 'student'))
                                        ->whereDoesntHave('student');
                                }
                            )
                            ->label('Select User Account')
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
                    ->description('Select Academic Year first, then select classroom.')
                    ->schema([
                        // 2. // GANTI DI SINI: Kita jadikan Academic Year sebagai input SELECT (bukan placeholder)
                        // Karena di database kamu ada kolom academic_year_id, ini harus diisi agar tersimpan.
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Academic Year')
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->live() // Agar saat tahun dipilih, pilihan Classroom di bawah otomatis berubah
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('classroom_id', null)), // Reset kelas jika tahun diubah

                        Forms\Components\Select::make('classroom_id')
                            ->label('Classroom')
                            ->required()
                            ->live()
                            // 3. // TAMBAHAN LOGIKA: Filter Classroom berdasarkan Academic Year yang dipilih di atas
                            ->options(function (Forms\Get $get) {
                                $academicYearId = $get('academic_year_id');
                                if (! $academicYearId) {
                                    return []; // Jika tahun belum dipilih, kelas kosong
                                }

                                // Ambil kelas yang HANYA ada di tahun ajaran tersebut
                                return Classroom::where('academic_year_id', $academicYearId)
                                    ->get()
                                    ->mapWithKeys(function ($classroom) {
                                        // Menampilkan nama kelas + jurusan agar tidak tertukar (Contoh: TI-1 - Teknik Informatika)
                                        return [$classroom->id => $classroom->name . ' - ' . ($classroom->department->name ?? '')];
                                    });
                            })
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if (! $state) {
                                    return;
                                }
                                $classroom = Classroom::find($state);
                                if ($classroom) {
                                    // 4. // OTOMATIS: Mengisi department_id sesuai kelas yang dipilih
                                    $set('department_id', $classroom->department_id);
                                }
                            }),

                        // 5. // GANTI DI SINI: Department tetap harus ada (karena ada di migration),
                        // tapi kita buat Disabled (agar admin tidak ubah-ubah manual) namun tetap tersimpan (dehydrated)
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->required()
                            ->disabled()
                            ->dehydrated() // Penting! Agar data tetap dikirim ke database saat simpan
                            ->placeholder('Will be auto-filled by system.'),

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Student Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Student Name')
                            ->weight('bold')
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('nis')
                            ->label('NIS'),
                        Infolists\Components\TextEntry::make('gender')
                            ->badge(),
                        Infolists\Components\TextEntry::make('birth_date')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Academic Detail')
                    ->schema([
                        Infolists\Components\TextEntry::make('classroom.name')
                            ->label('Classroom'),
                        Infolists\Components\TextEntry::make('department.name')
                            ->label('Department'),
                        Infolists\Components\TextEntry::make('academicYear.name')
                            ->label('Academic Year'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Contact & Guardian Info')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('parent_name')
                            ->label('Parent Name'),
                        Infolists\Components\TextEntry::make('parent_phone')
                            ->label('Parent Phone'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull()
                            ->placeholder('Not provided'),
                    ])->columns(2),
            ]);
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
