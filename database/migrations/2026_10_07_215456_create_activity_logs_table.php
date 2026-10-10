<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('action', 100)
                  ->comment('Nama aksi: "Tambah Barang", "Barang Masuk", dll');

            $table->text('description')->nullable()
                  ->comment('Detail aksi');

            $table->string('reference', 100)->nullable()
                  ->comment('Kode dokumen terkait: BM-20261008-001');

            $table->timestamps();

            $table->index('created_at');
            $table->index('user_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};