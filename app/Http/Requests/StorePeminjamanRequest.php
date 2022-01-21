<?php

namespace App\Http\Requests;

use App\Models\Peminjaman;
use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
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
            'tanggal_peminjaman' => 'required|date',
            'lama_peminjaman' => 'required|integer',
            'tanggal_pengembalian' => 'nullable|integer',
            'nominal_denda' => 'nullable|numeric',
            'peminjam' => 'required|exists:App\Models\User,id'
        ];
    }
}
