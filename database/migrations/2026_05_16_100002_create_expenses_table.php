<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Nama pengeluaran
            $table->string('name');

            // Kategori: operasional, maintenance, dll
            $table->string('category', 50);

            // Nominal
            $table->decimal('amount', 12, 2);

            // Tanggal pengeluaran
            $table->date('expense_date');

            // Catatan
            $table->text('notes')->nullable();

            // Bukti pengeluaran (receipt)
            $table->string('receipt')->nullable();

            // Dicatat oleh admin/bendahara
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index performa
            $table->index('category');
            $table->index('expense_date');
            $table->index('deleted_at');
            $table->index(['expense_date', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};