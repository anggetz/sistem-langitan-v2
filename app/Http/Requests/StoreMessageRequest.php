<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_penerima' => 'required|exists:pengguna,id_pengguna|different:' . auth('api')->user()?->id_pengguna,
            'tema' => 'nullable|string|max:255',
            'isi_pesan' => 'required|string|max:5000',
            'id_replay' => 'nullable|exists:messages,id_message'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'id_penerima.required' => 'Penerima pesan harus diisi.',
            'id_penerima.exists' => 'Penerima pesan tidak ditemukan.',
            'id_penerima.different' => 'Tidak dapat mengirim pesan ke diri sendiri.',
            'tema.max' => 'Tema pesan maksimal 255 karakter.',
            'isi_pesan.required' => 'Isi pesan harus diisi.',
            'isi_pesan.max' => 'Isi pesan maksimal 5000 karakter.',
            'id_replay.exists' => 'Pesan yang direply tidak ditemukan.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate replay message access if provided
            if ($this->id_replay) {
                $originalMessage = \App\Models\Message::where('id_message', $this->id_replay)
                    ->where(function ($query) {
                        $query->where('id_pengirim', auth('api')->user()?->id_pengguna)
                            ->orWhere('id_penerima', auth('api')->user()?->id_pengguna);
                    })->first();

                if (!$originalMessage) {
                    $validator->errors()->add('id_replay', 'Anda tidak memiliki akses ke pesan yang akan direply.');
                }
            }
        });
    }
}
