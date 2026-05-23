@extends('layouts.student')

@section('title', 'Detail Transaksi')

@section('content')

    {{-- Back --}}
    <a href="{{ route('student.transactions') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Riwayat
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Detail Transaksi ──────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info Card --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-semibold text-white">Detail Transaksi</h2>
                    {{-- Status Badge --}}
                    @php $status = $transaction->payment_status; @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                        {{ match($status->color()) {
                            'success' => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'warning' => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
                            'danger'  => 'bg-rose-500/10 text-rose-400 ring-1 ring-rose-500/20',
                            default   => 'bg-slate-500/10 text-slate-400 ring-1 ring-slate-500/20',
                        } }}">
                        {{ $status->label() }}
                    </span>
                </div>

                <div class="px-5 py-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Transaksi</span>
                        <span class="font-mono text-white text-xs">{{ $transaction->code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jurusan</span>
                        <span class="text-white">{{ $transaction->department?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Semester</span>
                        <span class="text-white">Semester {{ $transaction->department?->semester ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-800 pt-3">
                        <span class="text-slate-400">Total Tagihan</span>
                        <span class="font-bold text-emerald-500 text-base">
                            Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($transaction->payment_method)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Metode Bayar</span>
                            <span class="text-white capitalize">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                        </div>
                    @endif
                    @if($transaction->paid_at)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Tanggal Lunas</span>
                            <span class="text-white">{{ $transaction->paid_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-400">Dibuat</span>
                        <span class="text-slate-300">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Upload Bukti Bayar — hanya muncul kalau pending --}}
            @if($transaction->isPending())
                <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-800">
                        <h3 class="font-medium text-white text-sm">Upload Bukti Bayar Manual</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Jika bayar via bagian keuangan minta kwitansi, upload bukti tanda pembayaran  untuk diverifikasi admin.
                        </p>
                    </div>
                    <form method="POST"
                          action="{{ route('student.transactions.upload-proof', $transaction) }}"
                          enctype="multipart/form-data"
                          class="px-5 py-4 space-y-3">
                        @csrf

                        @if($transaction->proof_of_payment)
                            <div class="flex items-center gap-2 text-xs text-emerald-400 bg-emerald-500/10 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                Bukti sudah diupload — menunggu verifikasi admin
                            </div>
                        @endif

                        <div>
                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full text-sm text-slate-400 file:mr-3 file:py-2 file:px-4
                                          file:rounded-lg file:border-0 file:text-xs file:font-medium
                                          file:bg-slate-800 file:text-slate-300 hover:file:bg-slate-700
                                          file:cursor-pointer cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF. Maks 5MB</p>
                            @error('proof')
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium
                                       py-2.5 rounded-xl transition-colors">
                            Upload Bukti Bayar
                        </button>
                    </form>
                </div>
            @endif

            {{-- Payment Logs --}}
            @if($transaction->paymentLogs->count() > 0)
                <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-800">
                        <h3 class="font-medium text-white text-sm">Riwayat Pembayaran</h3>
                    </div>
                    <div class="divide-y divide-slate-800">
                        @foreach($transaction->paymentLogs->sortByDesc('created_at') as $log)
                            <div class="px-5 py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-slate-300 capitalize font-medium">{{ $log->status }}</span>
                                    <p class="text-slate-500 mt-0.5 font-mono">{{ $log->midtrans_order_id }}</p>
                                </div>
                                <span class="text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Action Panel ──────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Bayar via Midtrans --}}
            @if($transaction->isPending())
                <div class="bg-slate-900 border border-emerald-500/20 rounded-xl p-5"
                     x-data="paymentHandler({{ $transaction->id }})">

                    <h3 class="font-semibold text-white mb-1">Bayar Sekarang</h3>
                    <p class="text-sm text-slate-400 mb-4">
                        Selesaikan pembayaran via transfer bank atau metode lainnya.
                    </p>

                    <p class="text-2xl font-bold text-emerald-500 mb-4">
                        Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                    </p>

                    <button @click="pay()"
                            :disabled="loading"
                            class="w-full flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </template>
                        <template x-if="loading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Memproses...' : 'Bayar via Midtrans'"></span>
                    </button>

                    {{-- Status message --}}
                    <div x-show="message" x-transition
                         class="mt-3 rounded-lg px-3 py-2 text-xs text-center"
                         :class="{
                             'bg-emerald-500/10 text-emerald-400': messageType === 'success',
                             'bg-rose-500/10 text-rose-400':       messageType === 'error',
                             'bg-amber-500/10 text-amber-400':     messageType === 'pending',
                         }"
                         x-text="message">
                    </div>

                    <p class="text-xs text-slate-600 text-center mt-3">
                        Didukung oleh <span class="text-slate-400">Midtrans</span> · Pembayaran aman & terenkripsi
                    </p>
                </div>
            @elseif($transaction->isPaid())
                <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-xl p-5 text-center">
                    <svg class="w-10 h-10 text-emerald-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-semibold text-emerald-400">Pembayaran Lunas</p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $transaction->paid_at?->format('d M Y H:i') }}
                    </p>
                </div>
            @else
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
                    <p class="text-slate-400 text-sm">{{ $transaction->payment_status->label() }}</p>
                </div>
            @endif

        </div>
    </div>

@endsection

@push('scripts')
{{-- Load Snap.js hanya kalau transaksi pending --}}
@if($transaction->isPending())
@php
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $snapUrl }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
function paymentHandler(transactionId) {
    return {
        loading:     false,
        message:     '',
        messageType: 'success',

        async pay() {
            this.loading = true;
            this.message = '';

            try {
                // 1. Request snap token dari server
                const res = await fetch(`/transactions/${transactionId}/snap-token`, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':           'application/json',
                    },
                });

                const data = await res.json();

                if (! res.ok) {
                    throw new Error(data.message || 'Gagal generate token');
                }

                this.loading = false;

                // 2. Buka Snap popup
                window.snap.pay(data.snap_token, {
                    onSuccess: (result) => {
                        this.message     = '✅ Pembayaran berhasil! Halaman akan diperbarui...';
                        this.messageType = 'success';
                        // Reload setelah 2 detik agar status terbaru tampil
                        setTimeout(() => location.reload(), 2000);
                    },
                    onPending: (result) => {
                        this.message     = '⏳ Pembayaran pending. Selesaikan sesuai instruksi.';
                        this.messageType = 'pending';
                    },
                    onError: (result) => {
                        this.message     = '❌ Pembayaran gagal. Coba lagi.';
                        this.messageType = 'error';
                    },
                    onClose: () => {
                        this.message     = '⚠️ Popup ditutup. Klik bayar lagi untuk melanjutkan.';
                        this.messageType = 'pending';
                    },
                });

            } catch (err) {
                this.loading     = false;
                this.message     = '❌ ' + err.message;
                this.messageType = 'error';
            }
        }
    }
}
</script>
@endif
@endpush
