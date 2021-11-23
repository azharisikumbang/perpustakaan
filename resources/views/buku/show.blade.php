<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Detail - Data Buku') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="my-4">
                <span class="text-sm font-normal text-gray-500">Kode Buku : {{ $kode }}</span>
                <h2 class="text-2xl font-bold text-gray-900 mb-2 uppercase">{{ $judul }}</h2>
            </div>
            <div class="mb-4 grid grid-cols-3 gap-4">
                <div class="w-full">
                    @if($sampul)
                        <img src="{{ asset('storage/images/sampul/' . $sampul) }}" class="w-full max-w-full">
                    @else
                        <x-no-cover size="large" />
                    @endif
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">ISBN</div>
                        <div class="text-base font-semibold text-gray-900">{{ $isbn }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Pengarang</div>
                        <div class="text-base font-semibold text-gray-900">{{ $pengarang }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Penerbit</div>
                        <div class="text-base font-semibold text-gray-900">{{ $penerbit }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Tahun Terbit</div>
                        <div class="text-base font-semibold text-gray-900">{{ $tahun_terbit }}</div>
                    </div>
                </div>
                <div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Nomor Rak</div>
                        <div class="text-base font-semibold text-gray-900">{{ $rak['kode'] }} - {{ $rak['alias'] }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Stok</div>
                        <div class="text-base font-semibold text-gray-900">{{ $stok }} unit</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Catatan Peminjaman</div>
                        <div class="text-base font-semibold text-gray-900">3 kali</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Tanggal Masuk</div>
                        <div class="text-base font-semibold text-gray-900">{{ date('d/m/Y', strtotime($tanggal_masuk)) }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-normal text-gray-500">Dimasukkan Oleh</div>
                        <div class="text-base font-semibold text-gray-900">Administrator</div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <!-- @TODO implementasi peminjaman lewat tombol berikut -->
                <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    Pinjam Buku Ini
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
