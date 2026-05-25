<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\Invoice;
use App\Observers\TransactionObserver;
use App\Observers\InvoiceObserver;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // =========================================
        // View Composer: Notifikasi untuk Student Layout
        // Share data notifikasi ke semua view yang memakai layouts.student
        // =========================================

        View::composer('layouts.student', function ($view) {
            if (!auth()->check()) return;

            $user = auth()->user();

            // Tagihan belum dibayar
            $student = $user->student;
            $navNotifications = collect();
            $notifCount = 0;

            if ($student) {
                $unpaidInvoices = Invoice::where('student_id', $student->id)
                    ->whereIn('status', ['unpaid', 'overdue'])
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
                    ->get();

                foreach ($unpaidInvoices as $inv) {
                    $navNotifications->push([
                        'type'    => 'invoice',
                        'title'   => 'Tagihan Belum Dibayar',
                        'message' => 'Invoice ' . $inv->number . ' · Rp ' . number_format((float) $inv->amount, 0, ',', '.'),
                        'time'    => 'Jatuh tempo: ' . optional($inv->due_date)->format('d M Y'),
                        'dot'     => $inv->status->value === 'overdue' ? 'bg-rose-400' : 'bg-amber-400',
                    ]);
                }

                // Transaksi pending menunggu verifikasi
                $pendingTrx = Transaction::where('user_id', $user->id)
                    ->pending()
                    ->latest()
                    ->limit(3)
                    ->get();

                foreach ($pendingTrx as $trx) {
                    $navNotifications->push([
                        'type'    => 'transaction',
                        'title'   => 'Menunggu Verifikasi Admin',
                        'message' => $trx->code . ' · Rp ' . number_format((float) $trx->amount, 0, ',', '.'),
                        'time'    => $trx->created_at->diffForHumans(),
                        'dot'     => 'bg-blue-400',
                    ]);
                }

                $notifCount = $navNotifications->count();
            }

            $view->with(compact('navNotifications', 'notifCount'));
        });

        // =========================================
        // Daftarkan Observer
        // Sesuai architecture.md: Observer Pattern
        // =========================================

        Invoice::observe(InvoiceObserver::class);
        Transaction::observe(TransactionObserver::class);

        // =========================================
        // Daftarkan Policy
        // Sesuai architecture.md: Policy-Based Authorization
        // =========================================

        Gate::policy(Transaction::class, TransactionPolicy::class);

        // =========================================
        // Daftarkan Policy untuk Role
        // =========================================
        Gate::policy(Role::class, RolePolicy::class);
    }
}
