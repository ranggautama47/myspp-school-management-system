<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Kode unik transaksi: TRX-20250511-A7K2M
            $table->string('code', 20)->unique();

            // Foreign keys
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete(); // Jangan hapus user jika masih ada transaksi

            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete(); // Jangan hapus department jika masih ada transaksi

            // Payment info
            $table->string('payment_method', 50)->nullable();

            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'cancelled',
            ])->default('pending');

            // Midtrans fields
            $table->string('snap_token')->nullable();
            $table->string('midtrans_url')->nullable();

            // Bukti bayar manual (upload siswa)
            $table->string('proof_of_payment')->nullable();

            // Waktu bayar — diisi saat status berubah jadi 'paid'
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index('code');
            $table->index('user_id');
            $table->index('department_id');
            $table->index('payment_status');
            $table->index('paid_at');
            $table->index('deleted_at');

            // Composite index untuk query umum: "transaksi pending milik user X"
            $table->index(['user_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
