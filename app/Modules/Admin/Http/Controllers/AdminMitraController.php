<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Modules\Admin\Http\Requests\StoreMitraRequest;
use App\Modules\Admin\Http\Requests\UpdateMitraRequest;
use App\Modules\Admin\Services\CreateMitra;
use App\Modules\Admin\Services\UpdateMitra;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminMitraController extends Controller
{
    public function __construct(
        private CreateMitra $createMitra,
        private UpdateMitra $updateMitra,
    ) {}

    public function index(): View
    {
        $daftarMitra = Mitra::query()
            ->with(['pengguna' => fn ($q) => $q->orderBy('id')])
            ->orderBy('nama')
            ->paginate(15);

        return view('admin::mitra.index', compact('daftarMitra'));
    }

    public function create(): View
    {
        return view('admin::mitra.create');
    }

    public function store(StoreMitraRequest $request): RedirectResponse
    {
        $mitra = $this->createMitra->handle($request->validated());

        return redirect()
            ->route('admin.mitra.edit', $mitra)
            ->with('status', "Mitra {$mitra->nama} dibuat. Link atur password dikirim ke {$request->validated('pic_email')}.");
    }

    public function edit(Mitra $mitra): View
    {
        $mitra->load('pengguna');

        return view('admin::mitra.edit', compact('mitra'));
    }

    public function update(UpdateMitraRequest $request, Mitra $mitra): RedirectResponse
    {
        $this->updateMitra->handle($mitra, $request->validated());

        return redirect()
            ->route('admin.mitra.edit', $mitra)
            ->with('status', 'Data mitra diperbarui.');
    }
}
