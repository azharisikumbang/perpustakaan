<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBukuRequest extends FormRequest
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
            'kode' => ['required', Rule::unique('buku')->ignore($this->buku->kode, 'kode')],
            'isbn' => 'required|string',
            'judul' => 'required|string',
            'sampul' => 'sometimes|image',
            'penerbit' => 'required|string',
            'pengarang' => 'required|string',
            'tahun_terbit' => 'required|size:4',
            'stok' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'rak_id' => 'required|exists:App\Models\Rak,id',
            'ddc_id' => 'required|exists:App\Models\DDC,id',
        ];
    }
}
