<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal Information')
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Profile Photo')
                        ->image()
                        ->imageEditor()
                        ->directory('profile-photos')
                        ->disk('public')
                        ->columnSpanFull()
                        ->avatar(),

                    Forms\Components\TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $context) => $context === 'create')
                        ->minLength(8)
                        ->helperText('Kosongkan jika tidak ingin mengubah password'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Role & Permission')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->options(
                            Role::all()->pluck('name', 'id')->map(fn($name) => ucfirst($name))
                        )
                        ->preload()
                        ->required()
                        ->helperText('Admin: akses penuh. Student: hanya akses portal siswa.'),
                ])
                ->columns(1),

            Forms\Components\Section::make('Documents')
                ->schema([
                    Forms\Components\FileUpload::make('scan_ijazah')
                        ->label('Scan Ijazah')
                        ->directory('scan-ijazah')
                        ->disk('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                        ->maxSize(5120)
                        ->downloadable()
                        ->openable()
                        ->previewable(true)
                        ->helperText('Format: JPG, PNG, PDF. Maks 5MB'),
                ])
                ->columns(1)
                ->collapsed(),
        ]);
    }

    // =========================================
    // TABLE
    // =========================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(
                        fn($record) =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=1D9E75&color=fff'
                    )
                    ->size(36),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->description(fn($record) => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn($state) => match ($state) {
                        'admin' => 'warning',
                        'student' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('-')
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Transactions')
                    ->counts('transactions')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    // =========================================
    // QUERY
    // =========================================

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with('roles'); // eager loading — hindari N+1
    }

    // =========================================
    // PAGES
    // =========================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::students()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }
}
