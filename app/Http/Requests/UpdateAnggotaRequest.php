<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnggotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama' => 'required', 
            'nomor_identitas' => ['required', Rule::unique('anggota')->ignore($this->keanggotaan->nomor_identitas, 'nomor_identitas')],
            'institusi' => 'required', 
            'alamat_institusi' => 'required', 
            'alamat_pribadi' => 'required', 
            'jenis_kelamin' => 'required|size:1', 
            'kontak' => 'required'
        ];
    }
}
