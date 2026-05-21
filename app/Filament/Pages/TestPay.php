<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TestPay extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    // Sembunyikan dari sidebar — hanya bisa diakses via tombol Test Pay
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.test-pay';

    // =========================================
    // STATE — terima query params dari redirect
    // =========================================

    public string $snapToken      = '';
    public int    $transactionId  = 0;
    public string $transactionCode = '';
    public int    $amount         = 0;

    public function mount(): void
    {
        // Ambil data dari query string
        $this->snapToken       = request()->query('token', '');
        $this->transactionId   = (int) request()->query('transaction_id', 0);
        $this->transactionCode = request()->query('code', '');
        $this->amount          = (int) request()->query('amount', 0);

        // Guard: kalau tidak ada token, redirect balik ke payments
        if (empty($this->snapToken)) {
            redirect()->route('filament.admin.resources.transactions.index');
        }
    }

    // =========================================
    // CALLBACK — dipanggil dari JS Snap setelah bayar
    // Livewire dispatch dari Blade view
    // =========================================

    public function paymentSuccess(array $result): void
    {
        $transaction = Transaction::find($this->transactionId);

        if ($transaction) {
            $transaction->markAsPaid($result['payment_type'] ?? 'midtrans');

            Notification::make()
                ->title('✅ Pembayaran Berhasil!')
                ->body("Transaksi {$this->transactionCode} telah lunas via Midtrans.")
                ->success()
                ->send();
        }

        // Redirect ke list transactions setelah success
        $this->redirect(route('filament.admin.resources.transactions.index'));
    }

    public function paymentPending(): void
    {
        Notification::make()
            ->title('⏳ Pembayaran Pending')
            ->body("Transaksi {$this->transactionCode} menunggu konfirmasi pembayaran.")
            ->warning()
            ->send();

        $this->redirect(route('filament.admin.resources.transactions.index'));
    }

    public function paymentError(string $message = ''): void
    {
        Notification::make()
            ->title('❌ Pembayaran Gagal')
            ->body($message ?: "Terjadi kesalahan saat proses pembayaran.")
            ->danger()
            ->send();
    }

    // =========================================
    // GETTER — dipakai di Blade view
    // =========================================

    public function getClientKey(): string
    {
        return config('services.midtrans.client_key', '');
    }

    public function getIsProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    public function getFormattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
