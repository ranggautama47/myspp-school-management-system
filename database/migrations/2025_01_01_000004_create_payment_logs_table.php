<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete(); // Log boleh ikut terhapus jika transaksi dihapus

            // Status dari Midtrans: settlement, pending, deny, expire, cancel
            $table->string('status', 50);

            // Simpan full JSON response dari Midtrans — pakai JSON agar queryable di MySQL 8
            $table->json('raw_response');

            // Order ID dari Midtrans (sama dengan transactions.code)
            $table->string('midtrans_order_id', 50);

            $table->timestamps(); // created_at saja yang penting, tapi biarkan updated_at

            // Index
            $table->index('transaction_id');
            $table->index('midtrans_order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
