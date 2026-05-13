<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\TransactionObserver;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // =========================================
        // Daftarkan Observer
        // Sesuai architecture.md: Observer Pattern
        // =========================================

        Transaction::observe(TransactionObserver::class);

        // =========================================
        // Daftarkan Policy
        // Sesuai architecture.md: Policy-Based Authorization
        // =========================================

        Gate::policy(Transaction::class, TransactionPolicy::class);
    }
}
