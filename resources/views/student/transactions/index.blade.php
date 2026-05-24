@extends('layouts.student')

@section('title', 'Payment History')
@section('page-title', 'Payment History')
@section('page-subtitle', 'Riwayat semua transaksi pembayaran kamu')

@section('content')

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-1.5 mb-5 bg-slate-950/60 border border-slate-800 rounded-xl p-1 w-fit">
        @foreach(['' => 'All', 'pending' => 'Pending', 'paid' => 'Paid', 'expired' => 'Expired'] as $value => $label)
            @php
                $isActive = request('status', '') === $value;
                $colors = match($value) {
                    'pending'  => 'text-amber-400',
                    'paid'     => 'text-emerald-400',
                    'expired'  => 'text-slate-400',
                    default    => 'text-white',
                };
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['status' => $value ?: null]) }}"
               class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all
                      {{ $isActive
                         ? 'bg-slate-800 text-white shadow-sm'
                         : 'text-slate-400 hover:text-slate-200' }}">
                {{ $label }}
                @if($value === 'pending' && !$isActive)
                    @php $pendingCount = \App\Models\Transaction::where('user_id', auth()->id())->pending()->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-1 bg-amber-500/20 text-amber-400 text-[10px] rounded-full px-1.5">{{ $pendingCount }}</span>
                    @endif
                @endif
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    @if($transactions->isEmpty())
        <div class="bg-slate-950 border border-slate-800 rounded-xl py-16 text-center">
            <svg class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-slate-500 text-sm font-medium">No transactions found</p>
            <p class="text-slate-600 text-xs mt-1">Try selecting a different filter</p>
        </div>
    @else
        <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/50">
                        <th class="text-left px-5 py-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Transaction</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Department</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($transactions as $trx)
                        @php $s = $trx->payment_status; @endphp
                        <tr class="hover:bg-slate-800/20 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0
                                        {{ match($s->color()) {
                                            'success' => 'bg-emerald-500/10',
                                            'warning' => 'bg-amber-500/10',
                                            default   => 'bg-slate-800',
                                        } }}">
                                        <svg class="w-3.5 h-3.5 {{ match($s->color()) { 'success' => 'text-emerald-500', 'warning' => 'text-amber-500', default => 'text-slate-500' } }}"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-mono text-xs text-slate-200 leading-none">{{ $trx->code }}</p>
                                        <p class="text-[10px] text-slate-500 mt-1 md:hidden">
                                            {{ $trx->department?->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-xs text-slate-300">{{ $trx->department?->name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Sem {{ $trx->department?->semester ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-400 hidden sm:table-cell">
                                {{ $trx->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="text-sm font-semibold {{ $s->color() === 'success' ? 'text-emerald-500' : 'text-slate-200' }}">
                                    Rp {{ number_format((float) $trx->amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold
                                    {{ match($s->color()) {
                                        'success' => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
                                        'warning' => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
                                        'danger'  => 'bg-rose-500/10 text-rose-400 ring-1 ring-rose-500/20',
                                        default   => 'bg-slate-800 text-slate-400 ring-1 ring-slate-700',
                                    } }}">
                                    {{ $s->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('student.transactions.show', $trx) }}"
                                   class="inline-flex items-center gap-1 text-[11px] text-slate-500 hover:text-emerald-400 font-medium transition-colors group-hover:text-slate-300">
                                    Detail
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($transactions->hasPages())
                <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-between">
                    <p class="text-xs text-slate-500">
                        Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                    </p>
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    @endif

@endsection
