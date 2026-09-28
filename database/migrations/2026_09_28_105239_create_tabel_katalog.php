<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 120)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('destinasi', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kategori_id')->nullable()
                ->constrained('kategori')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('slug', 170)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->string('status', 20)->default('aktif'); // aktif|nonaktif
            $table->timestamps();
        });

        Schema::create('gambar_destinasi', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('destinasi_id')->constrained('destinasi')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_sampul')->default(false);
            $table->timestamps();
        });

        Schema::create('jenis_tiket', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('destinasi_id')->constrained('destinasi')->restrictOnDelete();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('harga'); // rupiah
            $table->unsignedInteger('kuota_harian')->nullable();
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->string('status', 20)->default('aktif'); // aktif|nonaktif
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('paket_wisata', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()
                ->constrained('kategori')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('slug', 180);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi_hari')->default(1);
            $table->unsignedSmallInteger('durasi_malam')->default(0);
            $table->unsignedBigInteger('harga'); // rupiah, per peserta
            $table->unsignedInteger('min_peserta')->default(1);
            $table->unsignedInteger('maks_peserta')->nullable();
            $table->string('status', 20)->default('aktif'); // aktif|nonaktif
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });

        // Pivot many-to-many: satu paket bisa mencakup banyak destinasi,
        // satu destinasi bisa masuk di banyak paket.
        Schema::create('paket_destinasi', function (Blueprint $table): void {
            $table->foreignId('paket_id')->constrained('paket_wisata')->cascadeOnDelete();
            $table->foreignId('destinasi_id')->constrained('destinasi')->restrictOnDelete();
            $table->unsignedSmallInteger('urutan')->default(0); // urutan kunjungan dalam paket
            $table->timestamps();

            $table->primary(['paket_id', 'destinasi_id']);
        });

        Schema::create('gambar_paket', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paket_id')->constrained('paket_wisata')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_sampul')->default(false);
            $table->timestamps();
        });

        Schema::create('itinerari_paket', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paket_id')->constrained('paket_wisata')->cascadeOnDelete();
            $table->unsignedSmallInteger('hari_ke');
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index(['paket_id', 'hari_ke']);
        });

        Schema::create('jadwal_paket', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paket_id')->constrained('paket_wisata')->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->unsignedInteger('kuota');
            $table->unsignedInteger('kuota_terisi')->default(0);
            $table->string('status', 20)->default('dibuka'); // dibuka|penuh|ditutup
            $table->timestamps();

            $table->index(['paket_id', 'tanggal_berangkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_paket');
        Schema::dropIfExists('itinerari_paket');
        Schema::dropIfExists('gambar_paket');
        Schema::dropIfExists('paket_destinasi');
        Schema::dropIfExists('paket_wisata');
        Schema::dropIfExists('jenis_tiket');
        Schema::dropIfExists('gambar_destinasi');
        Schema::dropIfExists('destinasi');
        Schema::dropIfExists('kategori');
    }
};