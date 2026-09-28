<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pembayaran + ledger sederhana untuk split dana ke mitra:
 *   dompet_mitra      : saldo terkini per mitra (1 baris per mitra)
 *   transaksi_dompet  : histori kredit/debit (audit ringan, bukan append-only
 *                       ledger dengan delta available/held seperti referensi)
 *   pencairan_dana    : pengajuan & histori payout ke rekening mitra
 *
 * Tidak pakai idempotency_key di semua tabel & tidak ada verifikasi
 * signature payment gateway secara eksplisit di skema ini — disederhanakan
 * sesuai kebutuhan project, cukup dicek di service layer saat integrasi
 * payment gateway sungguhan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pesanan_id')->unique()
                ->constrained('pesanan')->restrictOnDelete();
            $table->string('metode', 30); // transfer_bank|e_wallet|qris|va
            $table->string('referensi', 100)->nullable()->unique();
            $table->unsignedBigInteger('jumlah_bayar');
            $table->string('status', 20)->default('menunggu'); // menunggu|berhasil|gagal|kedaluwarsa
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('dompet_mitra', function (Blueprint $table): void {
            // 1 baris per mitra -> tenant_id sebagai primary key, bukan tabel transaksi.
            $table->foreignId('tenant_id')->primary()
                ->constrained('mitra')->cascadeOnDelete();
            $table->unsignedBigInteger('saldo')->default(0);
            $table->timestamps();
        });

        Schema::create('pencairan_dana', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('mitra')->restrictOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->string('status', 20)->default('diajukan'); // diajukan|diproses|selesai|ditolak
            $table->string('rekening_tujuan', 150);
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('transaksi_dompet', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('mitra')->cascadeOnDelete();
            $table->string('tipe', 10); // kredit|debit
            $table->unsignedBigInteger('jumlah');
            // Snapshot saldo dompet_mitra SETELAH transaksi ini tercatat (audit ringan).
            $table->unsignedBigInteger('saldo_setelah');
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('pesanan_mitra_id')->nullable()
                ->constrained('pesanan_mitra')->nullOnDelete();
            $table->foreignId('pencairan_id')->nullable()
                ->constrained('pencairan_dana')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_dompet');
        Schema::dropIfExists('pencairan_dana');
        Schema::dropIfExists('dompet_mitra');
        Schema::dropIfExists('pembayaran');
    }
};