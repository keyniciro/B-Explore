<?php

namespace Tests\Feature\Auth;

use App\Models\Mitra;
use App\Models\User;
use App\Support\Auth\LoginLockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Skenario uji identitas & akses: wisatawan -> dashboard -> mitra.
 * Jalankan: php artisan test --filter=PortalAuthTest
 */
class PortalAuthTest extends TestCase
{
    use RefreshDatabase;

    private function wisatawan(array $extra = []): User
    {
        return User::factory()->create($extra); // role bawaan model = wisatawan, email terverifikasi
    }

    private function mitra(string $status = 'aktif'): User
    {
        $mitra = Mitra::unguarded(fn () => Mitra::create([
            'nama' => 'Wisata Uji', 'slug' => 'wisata-uji', 'email' => 'uji@mitra.test', 'status' => $status,
        ]));

        $user = User::factory()->create();
        $user->forceFill(['role' => User::ROLE_MITRA, 'tenant_id' => $mitra->id])->save();

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // ---------- halaman publik ----------

    public function test_halaman_publik_dan_form_dapat_dirender(): void
    {
        $this->get('/')->assertOk()->assertSee(route('login'), false)->assertSee(route('register'), false);
        $this->get(route('login'))->assertOk()->assertSee('name="portal" value="wisatawan"', false);
        $this->get(route('register'))->assertOk();
        $this->get(route('mitra.login'))->assertOk()->assertSee('name="portal" value="mitra"', false);
    }

    // ---------- registrasi ----------

    public function test_registrasi_menghasilkan_wisatawan_dan_role_tidak_bisa_disuntikkan(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',        // coba suntik
            'tenant_id' => 1,         // coba suntik
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'penyusup@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_WISATAWAN, $user->role);
        $this->assertNull($user->tenant_id);
        $this->assertAuthenticatedAs($user);
    }

    public function test_belum_verifikasi_email_dialihkan_ke_halaman_verifikasi(): void
    {
        $user = $this->wisatawan(['email_verified_at' => null]);

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('verification.notice'));
    }

    // ---------- login wisatawan ----------

    public function test_wisatawan_login_masuk_dashboard(): void
    {
        $user = $this->wisatawan();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'portal' => 'wisatawan'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk()->assertSee('Daftar Mitra');
    }

    public function test_pesan_galat_sama_untuk_email_salah_dan_sandi_salah(): void
    {
        $user = $this->wisatawan();

        $a = $this->from(route('login'))->post(route('login.store'), ['email' => $user->email, 'password' => 'salah']);
        $b = $this->from(route('login'))->post(route('login.store'), ['email' => 'tidakada@example.com', 'password' => 'salah']);

        $a->assertSessionHasErrors(['email' => 'Surel atau kata sandi tidak sesuai.']);
        $b->assertSessionHasErrors(['email' => 'Surel atau kata sandi tidak sesuai.']);
        $this->assertGuest();
    }

    public function test_akun_terkunci_setelah_lima_kali_gagal_meski_sandi_benar(): void
    {
        $user = $this->wisatawan();

        foreach (range(1, LoginLockout::MAX_FAILURES) as $i) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'salah']);
        }

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString('terkunci', session('errors')->first('email'));
    }

    // ---------- pemisahan portal ----------

    public function test_mitra_boleh_login_di_portal_wisatawan_tetapi_wisatawan_ditolak_di_portal_mitra(): void
    {
        $mitra = $this->mitra();
        $wisatawan = $this->wisatawan();

        // mitra = wisatawan + hak kelola: lewat /login biasa mendarat di dashboard wisatawan
        $this->post(route('login.store'), ['email' => $mitra->email, 'password' => 'password', 'portal' => 'wisatawan'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($mitra);
        Auth::logout();

        // wisatawan murni tidak punya akses mitra
        $this->post(route('login.store'), ['email' => $wisatawan->email, 'password' => 'password', 'portal' => 'mitra'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_mitra_login_masuk_dashboard_mitra(): void
    {
        $user = $this->mitra();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'portal' => 'mitra'])
            ->assertRedirect(route('mitra.dashboard'));

        $this->get(route('mitra.dashboard'))->assertOk()->assertSee('Wisata Uji');
    }

    public function test_mitra_login_setelah_tamu_membuka_dashboard_wisatawan_tetap_ke_dashboard_mitra(): void
    {
        $user = $this->mitra();

        $this->get(route('dashboard'))->assertRedirect(route('login')); // menyimpan intended=/dashboard

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'portal' => 'mitra'])
            ->assertRedirect(route('mitra.dashboard'));
    }

    public function test_mitra_nonaktif_kehilangan_akses_mitra_tetapi_tetap_wisatawan(): void
    {
        $user = $this->mitra('nonaktif');

        // portal mitra ditolak (setelah sandi benar)
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'portal' => 'mitra'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        // portal wisatawan tetap boleh
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'portal' => 'wisatawan'])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk()->assertSee('dinonaktifkan');
        $this->get(route('mitra.dashboard'))->assertForbidden();
    }

    public function test_akses_per_portal_dan_tamu_dialihkan_ke_login_yang_tepat(): void
    {
        // mitra: dashboard wisatawan DAN dashboard mitra
        $this->actingAs($this->mitra())->get(route('dashboard'))->assertOk();
        $this->get(route('mitra.dashboard'))->assertOk();
        Auth::logout();

        // wisatawan murni: tidak boleh ke portal mitra
        $this->actingAs($this->wisatawan())->get(route('mitra.dashboard'))->assertForbidden();
        Auth::logout();

        // tamu
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('mitra.dashboard'))->assertRedirect(route('mitra.login'));
        $this->get(route('mitra.daftar'))->assertRedirect(route('login'));
    }

    public function test_pengguna_login_yang_membuka_halaman_tamu_dikirim_ke_beranda(): void
    {
        $this->actingAs($this->wisatawan())->get(route('login'))->assertRedirect(route('dashboard'));
        Auth::logout();

        $mitra = $this->mitra();
        $this->actingAs($mitra)->get(route('login'))->assertRedirect(route('dashboard'));
        $this->get(route('mitra.login'))->assertRedirect(route('mitra.dashboard'));
    }

    public function test_wisatawan_login_membuka_login_mitra_dikembalikan_ke_dashboard_dengan_pesan(): void
    {
        $this->actingAs($this->wisatawan())->get(route('mitra.login'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');
    }

    // ---------- logout ----------

    public function test_logout_wisatawan_ke_beranda_dan_logout_mitra_ke_login_mitra(): void
    {
        $this->actingAs($this->wisatawan())->post(route('logout'), ['portal' => 'wisatawan'])
            ->assertRedirect(route('home'));
        $this->assertGuest();

        $this->actingAs($this->mitra())->post(route('logout'), ['portal' => 'mitra'])
            ->assertRedirect(route('mitra.login'));
        $this->assertGuest();

        // tombol "Login Mitra" di dashboard wisatawan = logout dengan portal=mitra
        $this->actingAs($this->wisatawan())->post(route('logout'), ['portal' => 'mitra'])
            ->assertRedirect(route('mitra.login'));
        $this->assertGuest();
    }

    // ---------- daftar mitra (Livewire) ----------

    public function test_daftar_mitra_membuat_tenant_dan_mengubah_role_user(): void
    {
        Storage::fake('public');
        $user = $this->wisatawan();

        Livewire::actingAs($user)->test('mitra::daftar')
            ->set('nama', 'Pantai Merah Tour')
            ->set('email', 'info@merah.test')
            ->set('telepon', '08123456789')
            ->set('logo', UploadedFile::fake()->createWithContent('logo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/q842iQAAAABJRU5ErkJggg==')))
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertRedirect(route('mitra.dashboard'));

        $user->refresh();
        $mitra = Mitra::where('email', 'info@merah.test')->firstOrFail();

        $this->assertSame(User::ROLE_MITRA, $user->role);
        $this->assertSame($mitra->id, $user->tenant_id);
        $this->assertSame('pantai-merah-tour', $mitra->slug);
        $this->assertSame('pending', $mitra->status);          // default migrasi, bukan dari input
        $this->assertSame('10.00', $mitra->persentase_komisi); // default migrasi, bukan dari input
        $this->assertTrue(Storage::disk('public')->exists($mitra->logo));
    }

    public function test_daftar_mitra_memvalidasi_input_dan_slug_dibuat_unik(): void
    {
        Mitra::unguarded(fn () => Mitra::create(['nama' => 'Kawah Ijen', 'slug' => 'kawah-ijen', 'email' => 'ada@mitra.test']));

        Livewire::actingAs($this->wisatawan())->test('mitra::daftar')
            ->call('simpan')
            ->assertHasErrors(['nama' => 'required', 'email' => 'required']);

        Livewire::actingAs($this->wisatawan())->test('mitra::daftar')
            ->set('nama', 'Kawah Ijen')
            ->set('email', 'ada@mitra.test')
            ->call('simpan')
            ->assertHasErrors(['email' => 'unique']);

        Livewire::actingAs($this->wisatawan())->test('mitra::daftar')
            ->set('nama', 'Kawah Ijen')
            ->set('email', 'baru@mitra.test')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('kawah-ijen-2', Mitra::where('email', 'baru@mitra.test')->value('slug'));
    }

    public function test_mitra_yang_membuka_halaman_daftar_dialihkan_ke_dashboard_mitra(): void
    {
        Livewire::actingAs($this->mitra())->test('mitra::daftar')
            ->assertRedirect(route('mitra.dashboard'));
    }

    public function test_aksi_daftar_ditolak_bila_akun_sudah_jadi_mitra_dari_tab_lain(): void
    {
        $user = $this->wisatawan();

        // halaman dibuka saat masih wisatawan...
        $komponen = Livewire::actingAs($user)->test('mitra::daftar')
            ->set('nama', 'Usaha Kedua')->set('email', 'kedua@mitra.test');

        // ...lalu akun sudah menjadi mitra (mis. dari tab/perangkat lain)
        $mitra = Mitra::unguarded(fn () => Mitra::create([
            'nama' => 'Usaha Pertama', 'slug' => 'usaha-pertama', 'email' => 'pertama@mitra.test',
        ]));
        $user->forceFill(['role' => User::ROLE_MITRA, 'tenant_id' => $mitra->id])->save();

        $komponen->call('simpan')->assertForbidden();

        $this->assertDatabaseMissing('mitra', ['email' => 'kedua@mitra.test']);
        $this->assertSame(1, Mitra::count());
    }

    public function test_daftar_mitra_tidak_mencabut_akses_wisatawan(): void
    {
        $user = $this->wisatawan();

        Livewire::actingAs($user)->test('mitra::daftar')
            ->set('nama', 'Usaha Baru')->set('email', 'baru@usaha.test')
            ->call('simpan')->assertHasNoErrors();

        // setelah jadi mitra, dashboard wisatawan TETAP bisa dibuka
        $this->actingAs($user->fresh())->get(route('dashboard'))->assertOk()->assertSee('Kelola Mitra');
    }

    public function test_dashboard_menampilkan_pintu_mitra_sesuai_status(): void
    {
        $this->actingAs($this->wisatawan())->get(route('dashboard'))
            ->assertSee('data-test="daftar-mitra-link"', false)
            ->assertDontSee('data-test="kelola-mitra-button"', false);
        Auth::logout();

        $this->actingAs($this->mitra())->get(route('dashboard'))
            ->assertSee('data-test="kelola-mitra-button"', false)
            ->assertDontSee('data-test="daftar-mitra-link"', false);
    }

    public function test_logout_dari_halaman_mitra_kembali_ke_login_mitra_dan_dari_dashboard_ke_beranda(): void
    {
        $user = $this->mitra();

        // portal tersembunyi di navbar mengikuti halaman asal
        $this->actingAs($user)->get(route('mitra.dashboard'))->assertSee('name="portal" value="mitra"', false);
        $this->get(route('dashboard'))->assertDontSee('name="portal" value="mitra"', false);
    }
}
