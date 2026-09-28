<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 150);
            $table->string('slug', 150)->unique();
            $table->string('email', 150)->unique();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('logo')->nullable();
            $table->text('deskripsi')->nullable();
            // Rate komisi platform saat ini (persen), di-snapshot ke pesanan_mitra tiap checkout.
            $table->decimal('persentase_komisi', 5, 2)->default(10.00);
            $table->string('status', 20)->default('pending'); // pending|aktif|nonaktif
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('customer')->after('password'); // customer|admin
            $table->foreignId('tenant_id')->nullable()->after('role')
                ->constrained('mitra')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('role');
        });

        Schema::dropIfExists('mitra');
    }
};