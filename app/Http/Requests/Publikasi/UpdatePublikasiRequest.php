<?php

namespace App\Http\Requests\Publikasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublikasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id_dosen' => auth()->user()->dosen?->id_dosen,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_dosen' => ['sometimes', 'required', 'integer', 'exists:dosen,id_dosen'],
            'judul' => ['sometimes', 'required', 'string', 'max:500'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tanggal_publikasi' => ['sometimes', 'required', 'date'],
            'id_jenis_publikasi' => ['sometimes', 'required', 'integer', 'exists:publikasi_jenis,id_jenis_publikasi'],
            'doi' => ['nullable', 'string', 'max:255'],
            'issn' => ['nullable', 'string', 'max:50'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'volume' => ['nullable', 'string', 'max:50'],
            'issue' => ['nullable', 'string', 'max:50'],
            'halaman' => ['nullable', 'string', 'max:50'],
            'abstrak' => ['nullable', 'string'],
            'kata_kunci' => ['nullable', 'string', 'max:500'],
            'bahasa' => ['nullable', 'string', 'max:50'],
            'pendanaan' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'url' => ['nullable', 'url', 'max:500'],
            'id_pengindeks_publikasi' => ['nullable', 'integer', 'exists:publikasi_pengindeks,id_pengindeks_publikasi'],
            'sjr_kuartil' => ['nullable', 'string', 'max:10'],
            'sinta' => ['nullable', 'string', 'max:10'],

            // Validasi untuk array penulis
            'penulis' => ['nullable', 'array'],
            'penulis.*.id_dosen' => ['nullable', 'integer', 'exists:dosen,id_dosen'],
            'penulis.*.nama' => ['required', 'string', 'max:255'],
            'penulis.*.afiliasi' => ['required', 'string', 'max:255'],
            'penulis.*.urutan' => ['required', 'integer', 'min:1'],
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
            'id_dosen.required' => 'ID dosen harus diisi',
            'id_dosen.integer' => 'ID dosen harus berupa angka',
            'id_dosen.exists' => 'Dosen tidak ditemukan',
            'judul.required' => 'Judul publikasi harus diisi',
            'judul.string' => 'Judul publikasi harus berupa teks',
            'judul.max' => 'Judul publikasi maksimal 500 karakter',
            'penerbit.string' => 'Penerbit harus berupa teks',
            'penerbit.max' => 'Penerbit maksimal 255 karakter',
            'tanggal_publikasi.required' => 'Tanggal publikasi harus diisi',
            'tanggal_publikasi.date' => 'Tanggal publikasi harus berupa tanggal yang valid',
            'id_jenis_publikasi.required' => 'Jenis publikasi harus diisi',
            'id_jenis_publikasi.integer' => 'Jenis publikasi harus berupa angka',
            'id_jenis_publikasi.exists' => 'Jenis publikasi tidak ditemukan',
            'doi.string' => 'DOI harus berupa teks',
            'doi.max' => 'DOI maksimal 255 karakter',
            'issn.string' => 'ISSN harus berupa teks',
            'issn.max' => 'ISSN maksimal 50 karakter',
            'isbn.string' => 'ISBN harus berupa teks',
            'isbn.max' => 'ISBN maksimal 50 karakter',
            'volume.string' => 'Volume harus berupa teks',
            'volume.max' => 'Volume maksimal 50 karakter',
            'issue.string' => 'Issue harus berupa teks',
            'issue.max' => 'Issue maksimal 50 karakter',
            'halaman.string' => 'Halaman harus berupa teks',
            'halaman.max' => 'Halaman maksimal 50 karakter',
            'abstrak.string' => 'Abstrak harus berupa teks',
            'kata_kunci.string' => 'Kata kunci harus berupa teks',
            'kata_kunci.max' => 'Kata kunci maksimal 500 karakter',
            'bahasa.string' => 'Bahasa harus berupa teks',
            'bahasa.max' => 'Bahasa maksimal 50 karakter',
            'pendanaan.string' => 'Pendanaan harus berupa teks',
            'pendanaan.max' => 'Pendanaan maksimal 255 karakter',
            'status.string' => 'Status harus berupa teks',
            'status.max' => 'Status maksimal 50 karakter',
            'url.url' => 'URL harus berupa URL yang valid',
            'url.max' => 'URL maksimal 500 karakter',
            'id_pengindeks_publikasi.integer' => 'Pengindeks publikasi harus berupa angka',
            'id_pengindeks_publikasi.exists' => 'Pengindeks publikasi tidak ditemukan',
            'sjr_kuartil.string' => 'SJR Kuartil harus berupa teks',
            'sjr_kuartil.max' => 'SJR Kuartil maksimal 10 karakter',
            'sinta.string' => 'SINTA harus berupa teks',
            'sinta.max' => 'SINTA maksimal 10 karakter',

            // Pesan validasi untuk penulis
            'penulis.array' => 'Data penulis harus berupa array',
            'penulis.*.id_dosen.integer' => 'ID dosen pada penulis harus berupa angka',
            'penulis.*.id_dosen.exists' => 'Dosen pada penulis tidak ditemukan',
            'penulis.*.nama.required' => 'Nama penulis harus diisi',
            'penulis.*.nama.string' => 'Nama penulis harus berupa teks',
            'penulis.*.nama.max' => 'Nama penulis maksimal 255 karakter',
            'penulis.*.afiliasi.required' => 'Afiliasi penulis harus diisi',
            'penulis.*.afiliasi.string' => 'Afiliasi penulis harus berupa teks',
            'penulis.*.afiliasi.max' => 'Afiliasi penulis maksimal 255 karakter',
            'penulis.*.urutan.required' => 'Urutan penulis harus diisi',
            'penulis.*.urutan.integer' => 'Urutan penulis harus berupa angka',
            'penulis.*.urutan.min' => 'Urutan penulis minimal 1',
        ];
    }
}
