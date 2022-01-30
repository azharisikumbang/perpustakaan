<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Halaman Laporan') }}
        </h2>
    </x-slot>

    <div class="mb-8">

        <form action="{{ route('laporan.generate') }}" method="post">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Tipe Laporan <span class="text-red-500">*</span></label>
                        <select name="tipe" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                            <option value="keanggotaan">Laporan Keanggotaan Pepustakaan</option>
                            <option value="data-buku">Laporan Data Buku</option>
                            <option value="peminjaman">Laporan Peminjaman</option>
                            <option value="pengembalian">Laporan Pengembalian</option>
                            <option value="pembayaran-denda">Laporan Pembayaran Denda</option>
                        </select>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="text-sm font-medium text-gray-900 block mb-2">Jumlah (baris) <span class="text-red-500">*</span></label>
                            <select name="limit" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                <option value="0">Seluruh Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="100">100 Baris</option>
                                <option value="500">500 Baris</option>
                                <option value="1000">1000 Baris</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">Periode <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <select name="periode_tahun" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                        <option value="0">-- Tahun --</option>
                                        <option value="0">-- Semua --</option>
                                        <?php for ($tahun = date('Y'); $tahun >= 2010; $tahun--) { ?>
                                        <option value="<?= $tahun ?>"><?= $tahun ?></option>
                                        <?php } ?>    
                                    </select>
                                </div>
                                <div>
                                    <select name="periode_bulan" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                        <option value="0">-- Bulan --</option>
                                        <option value="0">-- Semua --</option>
                                        <?php 
                                            $list_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                            foreach ($list_bulan as $key => $bulan) { ?>
                                        <option value="<?= ($key + 1) ?>"><?= $bulan ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div>
                                    <select name="periode_hari" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                        <option value="0">-- Tanggal --</option>
                                        <option value="0">-- Semua --</option>
                                        <?php for ($tanggal = 1; $tanggal <= 31 ; $tanggal++) { ?>
                                        <option value="{{ $tanggal }}">{{ $tanggal }}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                            Download Laporan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
