<?php

namespace App\Http\Middleware;

use App\Models\Pengaturan;
use Closure;
use Illuminate\Http\Request;

class EnsureStandardSettingWasConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        // @TODO
        // if (Pengaturan::count() < 1 && $request->path() != route('pengaturan.create') ) {
        //     return redirect()
        //         ->route('pengaturan.create')
        //         ->with(['status' => 0, 'messages' => 'Anda harus mengatur beberapa parameter terlebih dahulu.']);
        // }

        return $next($request);
    }
}
