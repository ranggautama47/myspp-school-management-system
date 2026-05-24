@extends('layouts.student')

@section('title', 'Detail Transaksi')
@section('page-title', $transaction->code)
@section('page-subtitle', 'Detail pembayaran SPP')

@section('content')

    {{-- Back --}}
    <a href="{{ route('student.transactions') }}"
       class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-300 mb-5 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Payment History
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT — Detail ──────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Transaction Info Card --}}
            <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">Transaction Details</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5 font-mono">{{ $transaction->code }}</p>
                    </div>
                    @php $status = $transaction->payment_status; @endphp
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold
                        {{ match($status->color()) {
                            'success' => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                            'warning' => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
                            'danger'  => 'bg-rose-500/10 text-rose-400 ring-1 ring-rose-500/20',
                            default   => 'bg-slate-800 text-slate-400 ring-1 ring-slate-700',
                        } }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ match($status->color()) { 'success' => 'bg-emerald-400', 'warning' => 'bg-amber-400', 'danger' => 'bg-rose-400', default => 'bg-slate-500' } }}"></span>
                        {{ $status->label() }}
                    </span>
                </div>

                <div class="divide-y divide-slate-800/60">
                    @php
                        $rows = [
                            ['label' => 'Department',   'value' => $transaction->department?->name ?? '-', 'mono' => false],
                            ['label' => 'Semester',     'value' => 'Semester ' . ($transaction->department?->semester ?? '-'), 'mono' => false],
                        ];
                    @endphp
                    @foreach($rows as $row)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-xs text-slate-500">{{ $row['label'] }}</span>
                            <span class="text-xs {{ $row['mono'] ? 'font-mono' : 'font-medium' }} text-slate-200">{{ $row['value'] }}</span>
                        </div>
                    @endforeach

                    <div class="px-5 py-4 flex items-center justify-between bg-slate-900/30">
                        <span class="text-xs text-slate-500">Total Amount</span>
                        <span class="text-lg font-bold text-emerald-500">
                            Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($transaction->payment_method)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Payment Method</span>
                            <span class="text-xs font-medium text-slate-200 capitalize">
                                {{ str_replace('_', ' ', $transaction->payment_method) }}
                            </span>
                        </div>
                    @endif

                    @if($transaction->paid_at)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Paid At</span>
                            <span class="text-xs font-medium text-emerald-400">
                                {{ $transaction->paid_at->format('d M Y · H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="px-5 py-3 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Created</span>
                        <span class="text-xs text-slate-400">{{ $transaction->created_at->format('d M Y · H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Upload Proof --}}
            @if($transaction->isPending())
                <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-800">
                        <h3 class="text-sm font-medium text-white">Upload Bukti Bayar</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Bayar via transfer manual? Upload bukti untuk diverifikasi admin.
                        </p>
                    </div>
                    <form method="POST"
                          action="{{ route('student.transactions.upload-proof', $transaction) }}"
                          enctype="multipart/form-data"
                          class="px-5 py-4 space-y-3">
                        @csrf

                        @if($transaction->proof_of_payment)
                            <div class="flex items-center gap-2.5 text-xs text-emerald-400 bg-emerald-500/8 border border-emerald-500/15 rounded-xl px-4 py-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Bukti sudah diupload — menunggu verifikasi admin
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-slate-700 hover:border-slate-600 rounded-xl p-4 transition-colors">
                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full text-xs text-slate-400
                                          file:mr-3 file:py-1.5 file:px-4
                                          file:rounded-lg file:border-0
                                          file:text-xs file:font-medium
                                          file:bg-slate-800 file:text-slate-300
                                          hover:file:bg-slate-700 file:cursor-pointer cursor-pointer">
                            <p class="text-[10px] text-slate-600 mt-2">JPG, PNG, PDF · Max 5MB</p>
                            @error('proof')
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium
                                       py-2.5 rounded-xl transition-colors border border-slate-700/60">
                            Upload Bukti Bayar
                        </button>
                    </form>
                </div>
            @endif

            {{-- Payment Logs --}}
            @if($transaction->paymentLogs->count() > 0)
                <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-800">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Payment Log</h3>
                    </div>
                    <div class="divide-y divide-slate-800/60">
                        @foreach($transaction->paymentLogs->sortByDesc('created_at') as $log)
                            <div class="px-5 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $log->status === 'settlement' ? 'bg-emerald-500' : 'bg-slate-600' }}"></div>
                                    <div>
                                        <span class="text-xs text-slate-300 font-medium capitalize">{{ $log->status }}</span>
                                        <p class="text-[10px] text-slate-600 font-mono mt-0.5">{{ Str::limit($log->midtrans_order_id, 30) }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-500">{{ $log->created_at->format('d M Y · H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ── RIGHT — Action Panel ────────────────────────────────── --}}
        <div class="space-y-4">

            @if($transaction->isPending())
                <div class="bg-slate-950 border border-emerald-500/15 rounded-xl overflow-hidden"
                     x-data="paymentHandler({{ $transaction->id }})">

                    <div class="px-5 py-4 border-b border-slate-800/60">
                        <h3 class="text-sm font-semibold text-white">Complete Payment</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Selesaikan via Midtrans Snap</p>
                    </div>

                    <div class="px-5 py-4">
                        <div class="bg-slate-900/60 rounded-xl px-4 py-3 mb-4 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Amount Due</span>
                            <span class="text-xl font-bold text-emerald-500">
                                Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <button @click="pay()"
                                :disabled="loading"
                                class="w-full flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600
                                       active:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed
                                       text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                            <template x-if="!loading">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
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

                        <div x-show="message" x-transition
                             class="mt-3 rounded-xl px-3 py-2 text-xs text-center font-medium"
                             :class="{
                                 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/15': messageType === 'success',
                                 'bg-rose-500/10 text-rose-400 border border-rose-500/15':           messageType === 'error',
                                 'bg-amber-500/10 text-amber-400 border border-amber-500/15':        messageType === 'pending',
                             }"
                             x-text="message">
                        </div>

                        <div class="flex items-center justify-center gap-1.5 mt-3">
                            <svg class="w-3 h-3 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <p class="text-[10px] text-slate-600">Secured by <span class="text-slate-500">Midtrans</span></p>
                        </div>
                    </div>
                </div>

            @elseif($transaction->isPaid())
                <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-xl p-6 text-center">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-emerald-400">Payment Complete</p>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $transaction->paid_at?->format('d M Y · H:i') ?? '-' }}
                    </p>
                </div>

            @else
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-5 text-center">
                    <p class="text-sm text-slate-400 font-medium">{{ $transaction->payment_status->label() }}</p>
                    <p class="text-xs text-slate-600 mt-1">This transaction cannot be paid</p>
                </div>
            @endif

            {{-- Quick links --}}
            <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
                <a href="{{ route('student.transactions') }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800/30 transition-colors group">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="text-xs text-slate-400 group-hover:text-slate-200 transition-colors">All Transactions</span>
                    <svg class="w-3.5 h-3.5 text-slate-600 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 border-t border-slate-800/60 hover:bg-slate-800/30 transition-colors group">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs text-slate-400 group-hover:text-slate-200 transition-colors">Dashboard</span>
                    <svg class="w-3.5 h-3.5 text-slate-600 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
@if($transaction->isPending())
@php
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $snapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
function paymentHandler(transactionId) {
    return {
        loading: false, message: '', messageType: 'success',
        async pay() {
            this.loading = true;
            this.message = '';
            try {
                const res = await fetch(`/transactions/${transactionId}/snap-token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal generate token');
                this.loading = false;
                window.snap.pay(data.snap_token, {
                    onSuccess: () => {
                        this.message = '✅ Pembayaran berhasil! Memperbarui halaman...';
                        this.messageType = 'success';
                        setTimeout(() => location.reload(), 2000);
                    },
                    onPending: () => {
                        this.message = '⏳ Menunggu pembayaran. Selesaikan sesuai instruksi.';
                        this.messageType = 'pending';
                    },
                    onError: () => {
                        this.message = '❌ Pembayaran gagal. Silakan coba lagi.';
                        this.messageType = 'error';
                    },
                    onClose: () => {
                        this.message = '⚠️ Popup ditutup. Klik bayar untuk melanjutkan.';
                        this.messageType = 'pending';
                    },
                });
            } catch (err) {
                this.loading = false;
                this.message = '❌ ' + err.message;
                this.messageType = 'error';
            }
        }
    }
}
</script>
@endif
@endpush
