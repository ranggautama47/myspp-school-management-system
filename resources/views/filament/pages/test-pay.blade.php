<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[400px]">
        <div class="w-full max-w-md p-8 bg-white border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700 rounded-2xl">
            <div class="text-center">
                {{-- Icon --}}
                <div class="inline-flex items-center justify-center w-16 h-16 mb-4 bg-primary-50 dark:bg-primary-900/30 rounded-full">
                    <x-filament::icon
                        icon="heroicon-o-credit-card"
                        class="w-8 h-8 text-primary-600 dark:text-primary-400"
                    />
                </div>

                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Konfirmasi Pembayaran
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Selesaikan pembayaran untuk pesanan Anda
                </p>
            </div>

            {{-- Detail Info --}}
            <div class="mt-8 space-y-4">
                <div class="flex justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400">Kode Transaksi</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $transactionCode }}</span>
                </div>

                <div class="flex justify-between py-2">
                    <span class="text-gray-500 dark:text-gray-400">Total Tagihan</span>
                    <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $this->getFormattedAmount() }}
                    </span>
                </div>
            </div>

            {{-- Tombol Bayar --}}
            <div class="mt-8">
                <x-filament::button
                    size="xl"
                    class="w-full"
                    id="pay-button"
                    icon="heroicon-m-shield-check"
                >
                    Bayar Sekarang
                </x-filament::button>

                <p class="mt-4 text-xs text-center text-gray-500">
                    Pembayaran aman didukung oleh <span class="font-semibold text-blue-600">Midtrans</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Script Midtrans Snap --}}
    @push('scripts')
        <script
            src="{{ $this->getIsProduction() ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $this->getClientKey() }}">
        </script>

        <script>
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function () {
                // Trigger Snap Popup
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        // Panggil function paymentSuccess di TestPay.php
                        @this.call('paymentSuccess', result);
                    },
                    onPending: function(result) {
                        // Panggil function paymentPending di TestPay.php
                        @this.call('paymentPending', result);
                    },
                    onError: function(result) {
                        // Panggil function paymentError di TestPay.php
                        @this.call('paymentError', result.status_message);
                    },
                    onClose: function() {
                        // Opsional: Jika user menutup popup tanpa bayar
                        console.log('User closed the popup without finishing the payment');
                    }
                });
            });

            // Otomatis buka popup saat halaman load (Opsional)
            /*
            window.onload = function() {
                payButton.click();
            };
            */
        </script>
    @endpush
</x-filament-panels::page>
