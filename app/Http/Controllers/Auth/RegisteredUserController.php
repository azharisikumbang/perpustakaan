<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required', 
            'kode' => 'required|unique:App\Models\Anggota,kode', 
            'institusi' => 'required', 
            'alamat_institusi' => 'required', 
            'alamat_pribadi' => 'required', 
            'jenis_kelamin' => 'required|size:1', 
            'kontak' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $anggota = Anggota::make([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'institusi' => $request->institusi,
            'alamat_institusi' => $request->alamat_institusi,
            'alamat_pribadi' => $request->alamat_pribadi,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kontak' => $request->kontak
        ]); 

        $user->anggota()->save($anggota);

        $user->assignRole(Role::ANGGOTA);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
