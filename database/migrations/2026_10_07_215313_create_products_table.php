<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Identitas
            $table->string('code', 50)->unique()->comment('Kode barang, contoh: BRG001');
            $table->string('name', 150)->comment('Nama barang');
            $table->text('description')->nullable();

            // Relasi master
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained('units')
                  ->nullOnDelete();
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete();
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained('locations')
                  ->nullOnDelete();

            // Stok
            $table->integer('stock')->default(0)
                  ->comment('Stok realtime, diupdate lewat transaksi');
            $table->integer('min_stock')->default(0)
                  ->comment('Batas minimal untuk peringatan');

            // Status
            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->comment('Jangan hapus barang yang sudah ada transaksi');

            $table->timestamps();

            // Index untuk performa filter & sorting
            $table->index('name');
            $table->index('status');
            $table->index(['status', 'stock']); // untuk filter stok habis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};