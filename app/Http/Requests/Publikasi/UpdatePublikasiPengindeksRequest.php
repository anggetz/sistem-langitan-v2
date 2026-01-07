<?php

namespace App\Http\Requests\Publikasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePublikasiPengindeksRequest extends FormRequest
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
        $id = $this->route('id');
        
        return [
            'pengindeks_publikasi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('publikasi_pengindeks', 'pengindeks_publikasi')
                    ->ignore($id, 'id_pengindeks_publikasi')
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
            'pengindeks_publikasi.required' => 'Pengindeks publikasi harus diisi',
            'pengindeks_publikasi.string' => 'Pengindeks publikasi harus berupa teks',
            'pengindeks_publikasi.max' => 'Pengindeks publikasi maksimal 255 karakter',
            'pengindeks_publikasi.unique' => 'Pengindeks publikasi sudah terdaftar',
        ];
    }
}
