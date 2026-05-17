<?php

namespace App\Filament\Resources;

use App\Enums\ExpenseCategory;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'category', 'notes'];
    }

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Expense Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Expense Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('contoh: Pembelian ATK Bulan Mei')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('category')
                        ->label('Category')
                        ->options(ExpenseCategory::options())
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('expense_date')
                        ->label('Expense Date')
                        ->required()
                        ->default(now())
                        ->maxDate(now()),

                    Forms\Components\TextInput::make('amount')
                        ->label('Amount (IDR)')
                        ->prefix('Rp')
                        ->required()
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$money($input, ".")',
                            'inputmode' => 'numeric',
                        ])
                        ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state)),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('receipt')
                        ->label('Receipt / Bukti')
                        ->directory('expenses/receipts')
                        ->disk('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                        ->maxSize(5120)
                        ->downloadable()
                        ->openable()
                        ->previewable(true)
                        ->helperText('Format: JPG, PNG, PDF. Maks 5MB')
                        ->columnSpanFull(),
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
            ->modifyQueryUsing(fn(Builder $query) => $query->with('recorder'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Expense Name')
                    ->description(fn($record) => $record->notes ? \Str::limit($record->notes, 50) : null)
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color()),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (IDR)')
                    ->money('IDR', locale: 'id')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('danger')   // merah — pengeluaran
                    ->sortable(),

                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label('Recorded By')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('receipt')
                    ->label('Receipt')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options(ExpenseCategory::options()),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn(Builder $query) => $query->thisMonth()),

                Tables\Filters\Filter::make('this_year')
                    ->label('This Year')
                    ->query(fn(Builder $query) => $query->thisYear()),

                Tables\Filters\Filter::make('expense_date')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('expense_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('expense_date', '<=', $data['until']));
                    }),
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
            ->defaultSort('expense_date', 'desc')
            ->striped();
    }

    // =========================================
    // QUERY
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    // =========================================
    // NAV BADGE — total this month
    // =========================================

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::thisMonth()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'gray';
    }
}