<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanJenisTiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $platformAdmin = is_null($this->user()->tenant_id);

        return [
            // Admin platform wajib memilih mitra; staff mitra diisi otomatis.
            'tenant_id' => [Rule::requiredIf($platformAdmin), 'nullable', 'exists:mitra,id'],
            'destinasi_id' => ['required', Rule::exists('destinasi', 'id')->where('status', 'aktif')],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'kuota_harian' => ['nullable', 'integer', 'min:1'],
            'berlaku_dari' => ['nullable', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_dari'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => 'mitra',
            'destinasi_id' => 'destinasi',
            'kuota_harian' => 'kuota harian',
            'berlaku_dari' => 'tanggal mulai berlaku',
            'berlaku_sampai' => 'tanggal akhir berlaku',
        ];
    }
}
