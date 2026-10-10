<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->string('batch_code', 100)->unique()
                  ->comment('Format: BRG001-20261008-001');

            $table->date('entry_date')
                  ->comment('Tanggal barang masuk');

            $table->date('expired_date')->nullable()
                  ->comment('Tanggal kadaluarsa (opsional)');

            $table->integer('qty_initial')->default(0)
                  ->comment('Qty saat batch dibuat');

            $table->integer('qty_remaining')->default(0)
                  ->comment('Qty sisa di batch ini');

            $table->decimal('price', 15, 2)->nullable()
                  ->comment('Harga beli per satuan');

            $table->string('source_type', 50)->nullable()
                  ->comment('in, opname, adjustment, initial');

            $table->unsignedBigInteger('source_id')->nullable()
                  ->comment('ID transaksi/opname/adjustment sumber');

            $table->text('notes')->nullable();

            $table->timestamps();

            // Index penting untuk FEFO
            $table->index(['product_id', 'expired_date']);
            $table->index(['product_id', 'qty_remaining']);
            $table->index(['expired_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};