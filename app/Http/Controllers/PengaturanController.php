<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengaturanRequest;
use App\Http\Requests\UpdatePengaturanRequest;
use App\Models\Pengaturan;

class PengaturanController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data = Pengaturan::first();

        if (is_null($data)) {
            $data = Pengaturan::factory()->create();
        }
       
        return view('pengaturan.edit', $data->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePengaturanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePengaturanRequest $request)
    {
        Pengaturan::first()->update($request->validated());

        return back()->with(['status' => 1, 'messages' => 'Berhasil menyimpan pengaturan.']);
    }
}
