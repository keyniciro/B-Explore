<?php

namespace Tests\Feature\Admin;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Jalankan: php artisan test --filter=VerifikasiMitraTest
 */
class VerifikasiMitraTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        return $admin;
    }

    /** Wisatawan yang sudah mengajukan mitra dengan status tertentu. */
    private function pengaju(string $status = 'pending'): User
    {
        $mitra = Mitra::unguarded(fn () => Mitra::create([
            'nama' => 'Usaha Uji', 'slug' => 'usaha-uji', 'email' => 'uji@mitra.test', 'status' => $status,
        ]));

        $user = User::factory()->create();
        $user->forceFill(['tenant_id' => $mitra->id])->save();

        return $user;
    }

    public function test_hanya_admin_boleh_masuk_portal_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.login'))->assertOk()->assertSee('name="portal" value="admin"', false);

        $this->actingAs(User::factory()->create())->get(route('admin.verifikasi-mitra'))->assertForbidden();
        Auth::logout();

        $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.verifikasi-mitra'))->assertOk();
    }

    public function test_login_admin_lewat_form_admin(): void
    {
        $admin = $this->admin();

        $this->post(route('login.store'), [
            'email' => $admin->email, 'password' => 'password', 'portal' => 'admin',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_setujui_mengaktifkan_mitra_dan_menaikkan_role_pemilik(): void
    {
        $pengaju = $this->pengaju();
        $mitra = $pengaju->mitra;

        $this->actingAs($pengaju)->get(route('mitra.dashboard'))->assertForbidden();
        Auth::logout();

        Livewire::actingAs($this->admin())->test('admin::verifikasi-mitra')
            ->call('setujui', $mitra->id)
            ->assertSee('Mitra disetujui');

        $this->assertSame('aktif', $mitra->fresh()->status);
        $this->assertSame(User::ROLE_MITRA, $pengaju->fresh()->role);

        Auth::logout();
        $this->actingAs($pengaju->fresh())->get(route('mitra.dashboard'))->assertOk();
    }

    public function test_tolak_wajib_alasan_dan_role_tetap_wisatawan(): void
    {
        $pengaju = $this->pengaju();
        $mitra = $pengaju->mitra;

        Livewire::actingAs($this->admin())->test('admin::verifikasi-mitra')
            ->call('mulaiTolak', $mitra->id)
            ->call('tolak')
            ->assertHasErrors(['alasan' => 'required'])
            ->set('alasan', 'Dokumen usaha tidak lengkap, mohon dilengkapi.')
            ->call('tolak')
            ->assertHasNoErrors();

        $this->assertSame('ditolak', $mitra->fresh()->status);
        $this->assertSame(User::ROLE_WISATAWAN, $pengaju->fresh()->role);

        $this->actingAs($pengaju->fresh())->get(route('dashboard'))
            ->assertOk()->assertSee('Dokumen usaha tidak lengkap');
    }

    public function test_pengajuan_ditolak_bisa_diajukan_ulang(): void
    {
        $pengaju = $this->pengaju('ditolak');

        Livewire::actingAs($pengaju)->test('mitra::daftar')
            ->assertSet('nama', 'Usaha Uji')
            ->set('telepon', '08123456789')
            ->set('alamat', 'Jl. Contoh No. 1 Banyuwangi')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertSame('pending', $pengaju->mitra->fresh()->status);
        $this->assertSame(1, Mitra::count());
    }

    public function test_aksi_verifikasi_ditolak_untuk_non_admin(): void
    {
        $pengaju = $this->pengaju();

        Livewire::actingAs(User::factory()->create())->test('admin::verifikasi-mitra')->assertForbidden();

        $this->assertSame('pending', $pengaju->mitra->fresh()->status);
    }
}