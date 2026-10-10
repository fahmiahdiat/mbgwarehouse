<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_opname_id')
                  ->constrained('stock_opnames')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            // Snapshot stok sistem saat SO dibuat
            $table->integer('system_stock')
                  ->comment('Stok sistem saat opname dimulai');

            // Hasil hitung fisik
            $table->integer('physical_stock')->nullable()
                  ->comment('Stok fisik hasil hitung, null = belum diisi');

            // Selisih = fisik - sistem
            $table->integer('difference')->default(0)
                  ->comment('Otomatis dihitung: fisik - sistem');

            $table->string('notes', 255)->nullable();

            $table->timestamps();

            // Satu produk hanya boleh muncul 1x dalam 1 SO
            $table->unique(['stock_opname_id', 'product_id']);

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
    }
};