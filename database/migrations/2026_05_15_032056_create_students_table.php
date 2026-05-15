<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Relasi Akademik
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();

            // Data Demografi & Kontak
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Data Wali
            $table->string('parent_name');
            $table->string('parent_phone');

            // Status
            $table->enum('status', ['active', 'graduated', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
