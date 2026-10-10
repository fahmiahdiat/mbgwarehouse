<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();

            // Kode transaksi: BM-20261008-001, BK-..., RS-..., RG-...
            $table->string('code', 50)->unique();

            // Jenis transaksi
            $table->enum('type', [
                'in',              // Barang Masuk
                'out',             // Barang Keluar
                'return_supplier', // Retur ke supplier (stok keluar)
                'return_customer', // Retur ke gudang (stok masuk)
            ]);

            $table->date('date')->comment('Tanggal transaksi (bisa backdate)');

            // Untuk transaksi MASUK
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete();
            $table->string('delivery_note', 100)->nullable()
                  ->comment('Nomor surat jalan dari supplier');

            // Untuk transaksi KELUAR
            $table->string('recipient', 100)->nullable()
                  ->comment('Bagian/penerima barang');
            $table->string('purpose', 200)->nullable()
                  ->comment('Keperluan pengambilan');

            // Umum
            $table->text('notes')->nullable();

            // Siapa yang input (untuk audit)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // Index untuk laporan (paling sering di-query)
            $table->index(['type', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};