<?php

namespace App\Http\Requests\Publikasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublikasiJenisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis_publikasi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('publikasi_jenis', 'jenis_publikasi')
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis_publikasi.required' => 'Jenis publikasi harus diisi',
            'jenis_publikasi.string' => 'Jenis publikasi harus berupa teks',
            'jenis_publikasi.max' => 'Jenis publikasi maksimal 255 karakter',
            'jenis_publikasi.unique' => 'Jenis publikasi sudah terdaftar',
        ];
    }
}
