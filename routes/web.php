<?php

use App\Http\Controllers\AkunController;
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
use App\Http\Controllers\RiwayatPeminjamanController;
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

Route::group(['middleware' => ['auth', 'configured']], function() {

	// route pelanggan
	Route::get('/dashboard', [DashboardController::class, '__invoke'])->name('dashboard');

	Route::get('/akun', [AkunController::class, 'edit'])->name('akun.edit');
	Route::put('/akun', [AkunController::class, 'update'])->name('akun.update');
	
	Route::get('pencarian', [ PencarianController::class, 'index' ])->name('pencarian.index');
	Route::get('pencarian/redirect', [ PencarianController::class, 'show' ])->name('pencarian.show');
	
	Route::get('/buku', [ BukuController::class, 'index' ])->name('buku.index');
	Route::get('/buku/{buku}', [ BukuController::class, 'show' ])->name('buku.show');

	Route::get('/ddc', [ DDCController::class, 'index' ])->name('ddc.index');
	Route::get('/ddc/{ddc}', [ DDCController::class, 'show' ])->name('ddc.show');

	Route::get('/peminjaman', [ RiwayatPeminjamanController::class, 'index' ])->name('riwayat-peminjaman.index')->middleware('role:anggota');
	Route::get('/peminjaman/{peminjaman}', [ RiwayatPeminjamanController::class, 'show' ])->name('riwayat-peminjaman.show')->middleware('role:anggota');
	
	// route administrator
	Route::group(['prefix' => 'admin'], function() {

		// route kepala
		Route::group(['prefix' =>'laporan'], function() {
			Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
			Route::post('/', [LaporanController::class, 'generate'])->name('laporan.generate');
			Route::get('keanggotaan', [LaporanKeanggotaanController::class, '__invoke'])->name('laporan.keanggotaan');
		});

		Route::group([ 'middleware' => ['role:administrator'] ], function() {
			
			Route::resource('buku', BukuController::class)->except(['index', 'show']);
			Route::resource('ddc', DDCController::class)->except(['index', 'show']);
			Route::resource('rak', RakController::class)->except(['show']);
			Route::resource('peminjaman', PeminjamanController::class);

			// anggota
			Route::post('anggota/cek', [ CekAnggotaController::class, '__invoke' ])->name('anggota.cek');
			Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
			Route::get('anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
			Route::post('anggota', [AnggotaController::class, 'store'])->name('anggota.store');
			Route::get('anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');
			Route::get('anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
			Route::put('anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
			Route::delete('anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
			// Route::resource('anggota', AnggotaController::class);

			Route::get('pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
			Route::post('pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');

			Route::get('pengaturan', [ PengaturanController::class, 'edit' ])->name('pengaturan.edit');
			Route::put('pengaturan', [ PengaturanController::class, 'update' ])->name('pengaturan.update');

			Route::get('pembayaran/{peminjaman}', [PembayaranController::class, 'create'])->name('pembayaran.create');
			Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');

			Route::post('keranjang', [KeranjangBukuController::class, 'store'])->name('list.store');
			Route::delete('keranjang/{buku}', [KeranjangBukuController::class, 'remove'])->name('list.remove');
			Route::put('keranjang', [KeranjangBukuController::class, 'update'])->name('list.update');
		});

	});

});

require __DIR__.'/auth.php';
