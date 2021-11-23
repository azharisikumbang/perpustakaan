<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Edit - Data Buku') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('buku.update', ['buku' => $buku['id']]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Kode Buku <span class="text-red-500">*</span></label>
                <input type="text" name="kode" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('kode') ?? $buku['kode'] }}" required="">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">ISBN <span class="text-red-500">*</span></label>
                <input type="text" name="isbn" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('isbn') ?? $buku['isbn'] }}" required="">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Judul Buku <span class="text-red-500">*</span></label>
                <input type="text" name="judul" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('judul') ?? $buku['judul'] }}" required="">
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="penerbit" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('penerbit') ?? $buku['penerbit'] }}" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('pengarang') ?? $buku['pengarang'] }}" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun_terbit" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" size="4" value="{{ old('tahun_terbit') ?? $buku['tahun_terbit'] }}" required="">
                </div>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Tanggal Masuk Buka<span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('tanggal_masuk') ?? $buku['tanggal_masuk'] }}" required="">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Nomor Rak <span class="text-red-500">*</span></label>
                    <select name="rak_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                        <option value="">-- Pilih Rak --</option>
                        @foreach($listRak as $rak)
                            <option value="{{ $rak['id'] }}" {{ (old('rak_id') == $rak['id'] || $buku['rak_id'] == $rak['id']) ? 'selected' : ''}}>{{ $rak['kode'] }} - {{ $rak['alias'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-900 block mb-2">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stok" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('stok') ?? $buku['stok'] }}" required="">
                </div>
            </div>
            <div class="mb-4">
                <button class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    Perbaharui
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
