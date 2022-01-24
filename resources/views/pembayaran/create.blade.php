<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Ajukan Pembayaran Denda') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <form action="{{ route('pembayaran.store') }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Kode Peminjaman<span class="text-red-500">*</span></label>
                        <input type="text" name="kode" class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ $kode }}" readonly="">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nominal Bayar (Rupiah) <span class="text-red-500">*</span></label>
                        <input type="number" name="nominal" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('nominal') ?? '0' }}">
                    </div>
                    <div class="mb-4">
                        <button type='submit' class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                            Bayar
                        </button>
                    </div>
                </form>
            </div>
            <div class="px-4">
                <div class="text-xl font-bold mb-2">Informasi Keterlambatan</div>
                <div class="py-4 border-b border-gray-300 flex justify-between">
                    <div class="font-light text-gray-400">Hari Keterlambatan</div>
                    <div class="font-semibold">{{ $keterlambatan['hari'] }} hari</div>
                </div>
                <div class="py-4 border-b border-gray-300 flex justify-between">
                    <div class="font-light text-gray-400">Denda</div>
                    <div class="font-semibold">Rp. {{ number_format($keterlambatan['hari'] * $nominal_denda, 0, 2, ".") }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
