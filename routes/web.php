<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\CekAnggotaController;
use App\Http\Controllers\DDCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeranjangBukuController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKeanggotaanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RakController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect()->route('login');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'configured']], function() {
	// @TODO : merapikan route dashboard
	Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

	Route::resource('buku', BukuController::class);
	Route::resource('rak', RakController::class)->except(['show']);
	Route::resource('peminjaman', PeminjamanController::class);
	Route::resource('anggota', AnggotaController::class);
	Route::resource('ddc', DDCController::class);

	Route::get('pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
	Route::post('pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
	
	Route::post('keranjang', [KeranjangBukuController::class, 'store'])->name('list.store');
	Route::delete('keranjang/{buku}', [KeranjangBukuController::class, 'remove'])->name('list.remove');
	Route::put('keranjang', [KeranjangBukuController::class, 'update'])->name('list.update');

	Route::get('pembayaran/{peminjaman}', [PembayaranController::class, 'create'])->name('pembayaran.create');
	Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');

	Route::get('pencarian', [ PencarianController::class, 'index' ])->name('pencarian.index');
	Route::get('pencarian/redirect', [ PencarianController::class, 'show' ])->name('pencarian.show');
	
	Route::get('pengaturan', [ PengaturanController::class, 'edit' ])->name('pengaturan.edit');
	Route::put('pengaturan', [ PengaturanController::class, 'update' ])->name('pengaturan.update');

	// should be move to api routes
	Route::post('anggota/cek', [ CekAnggotaController::class, '__invoke' ])->name('anggota.cek');

	// laporan
	Route::group(['prefix' =>'laporan'], function() {
		Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
		Route::post('/', [LaporanController::class, 'generate'])->name('laporan.generate');
		Route::get('keanggotaan', [LaporanKeanggotaanController::class, '__invoke'])->name('laporan.keanggotaan');
	});
});

require __DIR__.'/auth.php';
