<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanJenisTiketRequest;
use App\Models\Destinasi;
use App\Models\JenisTiket;
use App\Models\Mitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JenisTiketController extends Controller
{
    // Catatan: TenantScope otomatis membatasi query untuk staff mitra,
    // sehingga edit/update/destroy tiket milik mitra lain akan 404.

    public function index(Request $request): View
    {
        $tiket = JenisTiket::with(['destinasi', 'mitra'])
            ->when($request->filled('cari'), fn ($q) => $q->where('nama', 'like', '%' . $request->cari . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.jenis-tiket.index', compact('tiket'));
    }

    public function create(): View
    {
        return view('admin.jenis-tiket.create', $this->dataForm(new JenisTiket(['status' => 'aktif'])));
    }

    public function store(SimpanJenisTiketRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->tenant_id) {
            $data['tenant_id'] = $request->user()->tenant_id;
        }

        JenisTiket::create($data);

        return redirect()->route('admin.jenis-tiket.index')
            ->with('sukses', 'Jenis tiket berhasil ditambahkan.');
    }

    public function edit(JenisTiket $jenisTiket): View
    {
        return view('admin.jenis-tiket.edit', $this->dataForm($jenisTiket));
    }

    public function update(SimpanJenisTiketRequest $request, JenisTiket $jenisTiket): RedirectResponse
    {
        $data = $request->validated();

        // Staff mitra tidak boleh memindahkan tiket ke mitra lain.
        if ($request->user()->tenant_id) {
            unset($data['tenant_id']);
        }

        $jenisTiket->update($data);

        return redirect()->route('admin.jenis-tiket.index')
            ->with('sukses', 'Jenis tiket berhasil diperbarui.');
    }

    public function destroy(JenisTiket $jenisTiket): RedirectResponse
    {
        // Tiket yang sudah pernah dipesan tidak dihapus, cukup dinonaktifkan,
        // supaya riwayat pesanan tetap utuh.
        $sudahDipesan = DB::table('item_pesanan')
            ->where('jenis_tiket_id', $jenisTiket->id)
            ->exists();

        if ($sudahDipesan) {
            return back()->with('gagal', 'Tiket ini sudah pernah dipesan dan tidak bisa dihapus. Ubah statusnya menjadi nonaktif.');
        }

        $jenisTiket->delete();

        return redirect()->route('admin.jenis-tiket.index')
            ->with('sukses', 'Jenis tiket berhasil dihapus.');
    }

    private function dataForm(JenisTiket $tiket): array
    {
        return [
            'tiket' => $tiket,
            'daftarDestinasi' => Destinasi::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama']),
            'daftarMitra' => auth()->user()->tenant_id ? collect() : Mitra::orderBy('nama')->get(['id', 'nama']),
        ];
    }
}
