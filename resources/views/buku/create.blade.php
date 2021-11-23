<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Tambah - Data Buku') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('buku.store') }}" method="post">
            @csrf
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Kode Buku <span class="text-red-500">*</span></label>
                <input type="text" name="kode" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">ISBN <span class="text-red-500">*</span></label>
                <input type="text" name="isbn" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Judul Buku <span class="text-red-500">*</span></label>
                <input type="text" name="judul" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="penerbit" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun_terbit" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" size="4" required="">
                </div>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Tanggal Masuk Buka<span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ date('Y-m-d') }}" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Nomor Rak <span class="text-red-500">*</span></label>
                    <select name="rak_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                        <option value="">-- Pilih Rak --</option>
                        @foreach($listRak as $rak)
                            <option value="{{ $rak['id'] }}">{{ $rak['kode'] }} - {{ $rak['alias'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stok" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                </div>
            </div>
            <div class="mb-4">
                <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    Tambah Buku
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
