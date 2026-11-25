<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Modules\Admin\Services\ChangeMitraStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

final class AdminMitraStatusController extends Controller
{
    public function __construct(private ChangeMitraStatus $service) {}

    public function __invoke(Request $request, Mitra $mitra): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ChangeMitraStatus::ALLOWED)],
        ]);

        $this->service->handle($mitra, $data['status']);

        return redirect()->route('admin.mitra.index')->with('status', $data['status'] === 'aktif'
            ? "Mitra {$mitra->nama} diaktifkan."
            : "Mitra {$mitra->nama} dinonaktifkan dan disembunyikan dari katalog.");
    }
}
