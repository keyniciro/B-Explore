<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table): void {
            $table->id();
            $table->string('nomor_pesanan', 30)->unique();
            $table->foreignId('pengguna_id')->constrained('users')->restrictOnDelete();
            // menunggu_pembayaran|dibayar|diproses|selesai|dibatalkan|kedaluwarsa
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('biaya_layanan')->default(0);
            $table->unsignedBigInteger('total_bayar')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'status']);
        });

        Schema::create('pesanan_mitra', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('mitra')->restrictOnDelete();
            // menunggu|diproses|selesai|dibatalkan
            $table->string('status', 20)->default('menunggu');
            $table->unsignedBigInteger('subtotal');
            // Snapshot rate komisi mitra.persentase_komisi pada saat checkout.
            $table->decimal('persentase_komisi_snapshot', 5, 2);
            $table->unsignedBigInteger('nominal_komisi');
            $table->unsignedBigInteger('nominal_diterima_mitra'); // subtotal - nominal_komisi
            $table->timestamps();

            $table->unique(['pesanan_id', 'tenant_id']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('item_pesanan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pesanan_mitra_id')->constrained('pesanan_mitra')->cascadeOnDelete();
            $table->string('jenis_produk', 20); // tiket|paket
            $table->foreignId('jenis_tiket_id')->nullable()
                ->constrained('jenis_tiket')->nullOnDelete();
            $table->foreignId('paket_id')->nullable()
                ->constrained('paket_wisata')->nullOnDelete();
            $table->foreignId('jadwal_paket_id')->nullable()
                ->constrained('jadwal_paket')->nullOnDelete();
            $table->string('nama_produk_snapshot', 150);
            $table->unsignedBigInteger('harga_satuan_snapshot');
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('subtotal');
            $table->date('tanggal_kunjungan')->nullable();
            $table->timestamps();

            $table->index(['pesanan_mitra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pesanan');
        Schema::dropIfExists('pesanan_mitra');
        Schema::dropIfExists('pesanan');
    }
};