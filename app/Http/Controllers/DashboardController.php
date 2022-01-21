<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
    	$pengaturan = Pengaturan::firstOrCreate();

    	if (!array_key_exists('lama_pinjaman', $pengaturan->attributesToArray())) {
    		return redirect()
    			->route('pengaturan.edit')
    			->with('message', 'Silahkan lakukan pengaturan terlebih dahulu untuk melanjutkan.');
    	}

    	return view('dashboard');
    }
}
