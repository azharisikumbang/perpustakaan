<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAnggotaRequest;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function edit()
    {
    	$user = auth()->user();
    	$user->load('anggota');

    	return view('akun.edit', $user->toArray());
    }

    public function update(UpdateAnggotaRequest $request)
    {
    	$validated = $request->validated();
    	$user = auth()->user();
    	$user->update(['name' => $validated['nama']]);
    	$user->anggota()->update($validated);

    	return back()->with(['status' => 1, 'messages' => 'Berhasil memperbaharui data akun.']);
    }
}
