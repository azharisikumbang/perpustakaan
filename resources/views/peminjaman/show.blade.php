<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
                Peminjaman <span class="text-gray-500 font-italic">#{{ $kode }}</span>
            </h2>
            <div>
                <a href="#" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">Ajukan Pengembalian</a>
            </div>
        </div>
    </x-slot>

    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="my-4 text-xl font-bold">Daftar Buku Dipinjam : </div>
            <div class="mb-4 flex justify-between">
                <div class="w-4/6 mr-4">
                    @foreach($buku as $detail_buku)
                        <div class="py-8 border-b border-gray-300 flex justify-between checkout-item">
                                <div class="w-1/4">
                                    <x-no-cover size="large" />
                                </div>
                                <div class="w-3/4">
                                    <div class="flex flex-wrap content-between h-full">
                                        <div class="w-full mb-4">
                                            <div class="font-semibold mb-2 text-xl">
                                                {{ $detail_buku['judul'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-gray-400">
                                                Kode : {{ $detail_buku['kode'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-gray-400">
                                                ISBN : {{ $detail_buku['isbn'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-gray-400">
                                                Rak : {{ $detail_buku['rak']['kode'] . '-' . $detail_buku['rak']['alias'] }}
                                            </div>
                                            <div class="font-light text-sm mb-2 text-gray-400">
                                                Jumlah : {{ $detail_buku['pivot']['jumlah'] }} buku dipinjam.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
                <div class="w-2/6 mt-8">
                    <div class="text-xl font-bold mb-2">Informasi Peminjaman</div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Tanggal Peminjaman</div>
                        <div class="font-semibold">{{ date('d/m/Y', strtotime($tanggal_peminjaman)) }}</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Jumlah Dipinjam</div>
                        <div class="font-semibold">{{ $total_buku }} buku</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Tanggal Pengembalian</div>
                        <div class="font-semibold">{{ date('d/m/Y', strtotime($keterlambatan['batas_pengembalian'])) }}</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Keterlambatan</div>
                        <div class="font-semibold">{{ $keterlambatan['terlambat'] ? $keterlambatan['hari'] . ' hari' : '-' }}</div>
                    </div>
                    <div class="py-4 border-b border-gray-300 flex justify-between">
                        <div class="font-light text-gray-400">Denda</div>
                        <div class="font-semibold">{{ $keterlambatan['terlambat'] ? 'Rp. ' . number_format($keterlambatan['hari'] * $nominal_denda, 0, 2, ".") : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
