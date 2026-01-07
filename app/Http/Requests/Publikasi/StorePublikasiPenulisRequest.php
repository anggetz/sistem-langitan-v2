<?php

namespace App\Http\Requests\Publikasi;

use Illuminate\Foundation\Http\FormRequest;

class StorePublikasiPenulisRequest extends FormRequest
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
            'id_publikasi' => ['required', 'integer', 'exists:publikasi,id_publikasi'],
            'id_dosen' => ['nullable', 'integer', 'exists:dosen,id_dosen'],
            'nama' => ['required', 'string', 'max:255'],
            'afiliasi' => ['nullable', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
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
            'id_publikasi.required' => 'ID publikasi harus diisi',
            'id_publikasi.integer' => 'ID publikasi harus berupa angka',
            'id_publikasi.exists' => 'Publikasi tidak ditemukan',
            'id_dosen.integer' => 'ID dosen harus berupa angka',
            'id_dosen.exists' => 'Dosen tidak ditemukan',
            'nama.required' => 'Nama penulis harus diisi',
            'nama.string' => 'Nama penulis harus berupa teks',
            'nama.max' => 'Nama penulis maksimal 255 karakter',
            'afiliasi.string' => 'Afiliasi harus berupa teks',
            'afiliasi.max' => 'Afiliasi maksimal 255 karakter',
            'urutan.required' => 'Urutan harus diisi',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 1',
        ];
    }
}
