<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitra', function (Blueprint $table): void {
            // status: pending | aktif | ditolak | nonaktif (kolom status sudah ada)
            $table->text('alasan_penolakan')->nullable()->after('status');
            $table->timestamp('ditinjau_pada')->nullable()->after('alasan_penolakan');
            $table->foreignId('ditinjau_oleh')->nullable()->after('ditinjau_pada')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('ditinjau_oleh');
            $table->dropColumn(['alasan_penolakan', 'ditinjau_pada']);
        });
    }
};