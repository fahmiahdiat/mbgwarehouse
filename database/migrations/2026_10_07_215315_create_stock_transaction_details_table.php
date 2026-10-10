<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transaction_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_transaction_id')
                  ->constrained('stock_transactions')
                  ->cascadeOnDelete()
                  ->comment('Kalau header dihapus, detail ikut terhapus');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete()
                  ->comment('Barang tidak boleh dihapus kalau masih dipakai transaksi');

            $table->integer('qty')->comment('Jumlah, selalu positif. Tanda +/- dari type header');

            $table->string('notes', 255)->nullable();

            $table->timestamps();

            // Index untuk pencarian kartu stok
            $table->index('product_id');
            $table->index('stock_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_details');
    }
};