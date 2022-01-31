<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Tambah - Data Klasifikasi DDC') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('ddc.store') }}" method="post">
            @csrf
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Kode Klasifikasi DDC <span class="text-red-500">*</span></label>
                <input type="text" name="kode" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Nama Klasifikasi DDC</label>
                <input type="text" name="klasifikasi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Jumlah Buku</label>
                <input type="number" name="jumlah" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
            <div class="mb-4">
                <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    Tambah Klasifikasi DDC
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
