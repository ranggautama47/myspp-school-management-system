@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">
            Selamat datang, {{ auth()->user()->name }} 👋
        </h1>
        <p class="text-slate-400 text-sm mt-1">
            @if($student)
                NIS: <span class="text-white font-mono">{{ $student->nis }}</span>
                · Kelas: <span class="text-white">{{ $student->classroom?->name ?? '-' }}</span>
                · {{ $student->department?->name ?? '-' }}
            @else
                Data siswa belum lengkap. Hubungi admin.
            @endif
        </p>
    </div>

    {{-- ── Summary Cards ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        {{-- Total Terbayar --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-emerald-500/10">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-slate-400">Total Terbayar</span>
            </div>
            <p class="text-2xl font-bold text-emerald-500">
                Rp {{ number_format((float) $totalPaid, 0, ',', '.') }}
            </p>
        </div>

        {{-- Tagihan Pending --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-amber-500/10">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-slate-400">Menunggu Bayar</span>
            </div>
            <p class="text-2xl font-bold text-amber-500">{{ $totalPending }}</p>
            <p class="text-xs text-slate-500 mt-1">transaksi pending</p>
        </div>

        {{-- Invoice Unpaid --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 rounded-lg bg-rose-500/10">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-sm text-slate-400">Invoice Belum Bayar</span>
            </div>
            <p class="text-2xl font-bold text-rose-500">{{ $pendingInvoices->count() }}</p>
            <p class="text-xs text-slate-500 mt-1">tagihan aktif</p>
        </div>
    </div>

    {{-- ── Tagihan Aktif (Invoice) ─────────────────────────────────── --}}
    @if($pendingInvoices->count() > 0)
        <div class="mb-8">
            <h2 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                Tagihan Aktif
            </h2>

            <div class="space-y-3">
                @foreach($pendingInvoices as $invoice)
                    <div class="bg-slate-900 border {{ $invoice->status->value === 'overdue' ? 'border-rose-500/50' : 'border-slate-800' }} rounded-xl p-5 flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-mono text-sm text-slate-300">{{ $invoice->number }}</p>
                                @if($invoice->status->value === 'overdue')
                                    <span class="text-xs bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-full px-2 py-0.5">
                                        Jatuh Tempo
                                    </span>
                                @else
                                    <span class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full px-2 py-0.5">
                                        Belum Dibayar
                                    </span>
                                @endif
                            </div>
                            <p class="text-white font-semibold">
                                Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $invoice->department?->name ?? '-' }}
                                · Jatuh tempo: {{ $invoice->due_date?->format('d M Y') }}
                            </p>

                            @if($invoice->notes)
                                <div class="mt-4 pt-3 border-t border-slate-700">
                                    <div class="flex items-start gap-2 text-sm text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>
                                            <span class="font-medium text-slate-300">Catatan dari Sekolah:</span> 
                                            {{ $invoice->notes }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if($invoice->transaction && $invoice->transaction->isPending())
                            <a href="{{ route('student.transactions.show', $invoice->transaction) }}"
                               class="shrink-0 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors text-center">
                                Menunggu Pembayaran
                            </a>
                        @else
                            <form action="{{ route('student.invoices.checkout', $invoice) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                                    Bayar
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Riwayat Transaksi Terbaru ────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-white">Transaksi Terbaru</h2>
            <a href="{{ route('student.transactions') }}"
               class="text-sm text-emerald-500 hover:text-emerald-400 font-medium">
                Lihat semua →
            </a>
        </div>

        @if($recentTransactions->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center">
                <p class="text-slate-400 text-sm">Belum ada transaksi.</p>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-800/50">
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Kode</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Jurusan</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Nominal</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($recentTransactions as $trx)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('student.transactions.show', $trx) }}"
                                       class="font-mono text-slate-300 hover:text-emerald-400 transition-colors text-xs">
                                        {{ $trx->code }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-slate-400">
                                    {{ $trx->department?->name ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-emerald-500">
                                    Rp {{ number_format((float) $trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php $status = $trx->payment_status; @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ match($status->color()) {
                                            'success' => 'bg-emerald-500/10 text-emerald-400',
                                            'warning' => 'bg-amber-500/10 text-amber-400',
                                            'danger'  => 'bg-rose-500/10 text-rose-400',
                                            default   => 'bg-slate-500/10 text-slate-400',
                                        } }}">
                                        {{ $status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
