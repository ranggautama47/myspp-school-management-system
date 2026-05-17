<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.manage-settings';

    // =========================================
    // ACCESS CONTROL — Super Admin only
    // canAccess() di PHP level, bukan hanya hide nav
    // =========================================

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Gunakan 'admin' (atau 'super-admin' tergantung seeder kamu)
        return $user?->hasRole('super-admin') ?? false;
    }

    // =========================================
    // STATE
    // =========================================

    public ?array $data = [];

    public function mount(): void
    {
        // Load semua setting dari DB, key sebagai index
        $settings = Setting::all()->keyBy('key');

        $this->form->fill([
            // General
            'school_name' => $settings->get('school_name')?->value,
            'school_address' => $settings->get('school_address')?->value,
            'school_phone' => $settings->get('school_phone')?->value,
            'school_email' => $settings->get('school_email')?->value,

            // Academic
            'academic_year' => $settings->get('academic_year')?->value,
            'default_capacity' => $settings->get('default_capacity')?->value,

            // Payment
            'default_spp_amount' => $settings->get('default_spp_amount')?->value,
            'midtrans_is_production' => (bool) ($settings->get('midtrans_is_production')?->value ?? false),

            // System
            'maintenance_mode' => (bool) ($settings->get('maintenance_mode')?->value ?? false),
            'timezone' => $settings->get('timezone')?->value ?? 'Asia/Jakarta',
            'default_theme' => $settings->get('default_theme')?->value ?? 'dark',
        ]);
    }

    // =========================================
    // FORM SCHEMA — Tabbed layout
    // =========================================

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([

                        // ── Tab: General ──────────────────────
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make('School Information')
                                    ->description('Informasi dasar sekolah yang tampil di aplikasi.')
                                    ->schema([
                                        Forms\Components\TextInput::make('school_name')
                                            ->label('School Name')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('SMA Negeri 1 ...'),

                                        Forms\Components\TextInput::make('school_email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(100)
                                            ->placeholder('admin@sekolah.sch.id'),

                                        Forms\Components\TextInput::make('school_phone')
                                            ->label('Phone')
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('021-xxxxxxxx'),

                                        Forms\Components\Textarea::make('school_address')
                                            ->label('Address')
                                            ->rows(3)
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->placeholder('Jl. ...'),
                                    ])
                                    ->columns(2),
                            ]),

                        // ── Tab: Academic ─────────────────────
                        Forms\Components\Tabs\Tab::make('Academic')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Section::make('Academic Configuration')
                                    ->description('Pengaturan tahun ajaran dan kapasitas kelas default.')
                                    ->schema([
                                        Forms\Components\TextInput::make('academic_year')
                                            ->label('Current Academic Year')
                                            ->required()
                                            ->placeholder('2024/2025')
                                            ->helperText('Format: YYYY/YYYY'),

                                        Forms\Components\TextInput::make('default_capacity')
                                            ->label('Default Class Capacity')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->suffix('students')
                                            ->helperText('Jumlah siswa default per kelas'),
                                    ])
                                    ->columns(2),
                            ]),

                        // ── Tab: Payment ──────────────────────
                        Forms\Components\Tabs\Tab::make('Payment')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\Section::make('SPP Configuration')
                                    ->description('Nominal SPP default yang dipakai saat membuat tagihan baru.')
                                    ->schema([
                                        Forms\Components\TextInput::make('default_spp_amount')
                                            ->label('Default SPP Amount')
                                            ->placeholder('2.500.000')

                                            ->prefix('Rp')

                                            ->extraInputAttributes([
                                                'x-mask:dynamic' => '$money($input, ".")',
                                                'inputmode' => 'numeric', // Memaksa keyboard angka muncul di HP
                                            ])
                                            ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state))
                                            // Memunculkan titik kembali saat memuat data dari Database (Mode Edit)
                                            ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                            ->helperText('Nominal SPP default per bulan (bisa di-override per kelas)'),
                                    ]),

                                Forms\Components\Section::make('Midtrans Configuration')
                                    ->description('Konfigurasi API Key diatur via .env. Toggle ini hanya untuk switch mode Sandbox/Production.')
                                    ->schema([
                                        Forms\Components\Toggle::make('midtrans_is_production')
                                            ->label('Production Mode')
                                            ->helperText('OFF = Sandbox (testing). ON = Production (live payment). Pastikan API key sudah sesuai.')
                                            ->onColor('danger')
                                            ->offColor('success'),
                                    ]),
                            ]),

                        // ── Tab: System ───────────────────────
                        Forms\Components\Tabs\Tab::make('System')
                            ->icon('heroicon-o-server')
                            ->schema([
                                Forms\Components\Section::make('System Configuration')
                                    ->description('Pengaturan sistem aplikasi secara keseluruhan.')
                                    ->schema([
                                        Forms\Components\Toggle::make('maintenance_mode')
                                            ->label('Maintenance Mode')
                                            ->helperText('Aktifkan untuk menutup akses portal siswa sementara waktu.')
                                            ->onColor('danger')
                                            ->offColor('success'),

                                        Forms\Components\Select::make('timezone')
                                            ->label('Timezone')
                                            ->options([
                                                'Asia/Jakarta' => 'WIB — Asia/Jakarta (UTC+7)',
                                                'Asia/Makassar' => 'WITA — Asia/Makassar (UTC+8)',
                                                'Asia/Jayapura' => 'WIT — Asia/Jayapura (UTC+9)',
                                            ])
                                            ->native(false)
                                            ->required(),

                                        Forms\Components\Select::make('default_theme')
                                            ->label('Default Theme')
                                            ->options([
                                                'dark' => '🌙 Dark',
                                                'light' => '☀️ Light',
                                            ])
                                            ->native(false)
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    // =========================================
    // SAVE ACTION
    // =========================================

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear semua setting cache agar perubahan langsung aktif
        Cache::flush();

        Notification::make()
            ->title('Settings saved')
            ->body('Konfigurasi aplikasi berhasil disimpan.')
            ->success()
            ->send();
    }

    // =========================================
    // HEADER ACTIONS
    // =========================================

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->action('save')
                ->color('primary'),
        ];
    }
}
