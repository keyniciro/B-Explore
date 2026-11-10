<?php

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMitraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->admin() ??false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama'              => ['required', 'string', 'max:150'],
            'email'             => ['required', 'email', 'max:150', Rule::unique('mitra', 'email')],
            'telepon'           => ['nullable', 'string', 'max:20'],
            'alamat'            => ['nullable', 'string', 'max:500'],
            'deskripsi'         => ['nullable', 'string', 'max:2000'],
            'persentase_komisi' => ['required', 'numeric', 'between:0,100'],
            'pic_nama'          => ['required', 'string', 'max:120'],
            'pic_email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nama'              => 'nama mitra',
            'persentase_komisi' => 'komisi',
            'pic_nama'          => 'nama penanggung jawab',
            'pic_email'         => 'email penanggung jawab',
            ];
    }
}
