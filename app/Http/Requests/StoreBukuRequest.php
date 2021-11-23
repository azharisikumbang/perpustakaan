<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
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
            'kode' => 'required|unique:App\Models\Buku,kode',
            'isbn' => 'required|string',
            'judul' => 'required|string',
            'penerbit' => 'required|string',
            'pengarang' => 'required|string',
            'tahun_terbit' => 'required|size:4',
            'stok' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'rak_id' => 'required|exists:App\Models\Rak,id'
        ];
    }
}
