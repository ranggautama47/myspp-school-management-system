<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Nomor invoice: INV-20250511-A7K2M
            $table->string('number', 25)->unique()->index();

            // Invoice ditujukan ke siswa
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Relasi ke department untuk nominal SPP
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Nominalnya
            $table->decimal('amount', 12, 2);

            // Batas bayar
            $table->date('due_date');

            // Status invoice
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])
                ->default('unpaid');

            // Jika sudah dibayar, link ke transaction
            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete();

            // Catatan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index performa
            $table->index('status');
            $table->index('due_date');
            $table->index('student_id');
            $table->index(['student_id', 'status']);
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
