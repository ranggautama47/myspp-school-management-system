@extends('layouts.student')

@section('title', 'Riwayat Pembayaran')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Riwayat Pembayaran</h1>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach(['' => 'Semua', 'pending' => 'Pending', 'paid' => 'Lunas', 'expired' => 'Kadaluarsa'] as $value => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $value ?: null]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ request('status', '') === $value
                         ? 'bg-emerald-500 text-white'
                         : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    @if($transactions->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-slate-500 text-sm">Belum ada transaksi.</p>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Kode</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide hidden sm:table-cell">Jurusan</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Nominal</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($transactions as $trx)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-mono text-xs text-slate-300">{{ $trx->code }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $trx->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-400 hidden sm:table-cell">
                                {{ $trx->department?->name ?? '-' }}
                                <span class="text-slate-600">· Sem {{ $trx->department?->semester }}</span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-500">
                                Rp {{ number_format((float) $trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php $s = $trx->payment_status; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ match($s->color()) {
                                        'success' => 'bg-emerald-500/10 text-emerald-400',
                                        'warning' => 'bg-amber-500/10 text-amber-400',
                                        'danger'  => 'bg-rose-500/10 text-rose-400',
                                        default   => 'bg-slate-500/10 text-slate-400',
                                    } }}">
                                    {{ $s->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('student.transactions.show', $trx) }}"
                                   class="text-xs text-emerald-500 hover:text-emerald-400 font-medium">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="px-5 py-4 border-t border-slate-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    @endif

@endsection
