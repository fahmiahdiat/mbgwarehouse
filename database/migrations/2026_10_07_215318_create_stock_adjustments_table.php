<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique()->comment('Format: ADJ-YYYYMMDD-XXX');
            $table->date('date');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            // Nilai penyesuaian: positif (+) tambah stok, negatif (-) kurangi stok
            $table->integer('adjustment')
                  ->comment('Bisa positif atau negatif, TIDAK BOLEH 0');

            $table->string('reason', 200)
                  ->comment('Alasan wajib, contoh: Selisih Stock Opname');

            $table->string('reference', 100)->nullable()
                  ->comment('Referensi ke dokumen lain, contoh: SO-20261008-001');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            $table->index(['product_id', 'date']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};