<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Pengaturan') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <form action="{{ route('pengaturan.update') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Lama Pinjaman (hari)<span class="text-red-500">*</span></label>
                        <input type="number" name="lama_pinjaman" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('lama_pinjaman') ?? $lama_pinjaman }}">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Jumlah Pinjaman <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_pinjaman" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('jumlah_pinjaman') ?? $jumlah_pinjaman }}">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nominal Denda (Rupiah) <span class="text-red-500">*</span></label>
                        <input type="number" name="nominal_denda" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('nominal_denda') ?? $nominal_denda }}">
                    </div>
                    <div class="mb-4">
                        <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-4">
                <div class="mb-2 text-base font-semibold text-gray-900">
                    Keterangan:
                </div>
                <div class="mb-2">
                    <div class="text-base font-semibold text-gray-900">Lama Pinjaman</div>
                    <div class="text-sm font-normal text-gray-500">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</div>
                </div>
                <div class="mb-2">
                    <div class="text-base font-semibold text-gray-900">Jumlah Pinjaman</div>
                    <div class="text-sm font-normal text-gray-500">Tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit ess.</div>
                </div>
                <div class="mb-2">
                    <div class="text-base font-semibold text-gray-900">Nominal Denda</div>
                    <div class="text-sm font-normal text-gray-500">Cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum sint occaecat cupidatat.</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
