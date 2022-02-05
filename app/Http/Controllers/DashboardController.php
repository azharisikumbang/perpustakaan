<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->hasRole(Role::ADMINISTRATOR)) {
            $pengaturan = Pengaturan::firstOrCreate();
            if (!array_key_exists('lama_pinjaman', $pengaturan->attributesToArray())) {
                return redirect()
                    ->route('pengaturan.edit')
                    ->with('messages', 'Silahkan lakukan pengaturan terlebih dahulu untuk melanjutkan.');
            }
        }

        if (auth()->user()->hasRole(Role::ANGGOTA)) {
            if (is_null(auth()->user()->anggota->institusi)) {
                return redirect()
                    ->route('akun.edit')
                    ->with('messages', 'Silahkan melengkapi biodata terlebih dahulu.');
            }

        }

    	return view('dashboard');
    }
}
