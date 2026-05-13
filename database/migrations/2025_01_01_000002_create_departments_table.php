<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('semester'); // 1-12, cukup tinyInteger
            $table->decimal('cost', 12, 2);          // max 9,999,999,999.99
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('name');
            $table->index('semester');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
