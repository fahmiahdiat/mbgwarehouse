<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique()->comment('Format: SO-YYYYMMDD-XXX');
            $table->date('date');

            $table->enum('status', ['draft', 'completed'])
                  ->default('draft')
                  ->comment('draft = masih diisi, completed = sudah fix');

            $table->text('notes')->nullable();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};