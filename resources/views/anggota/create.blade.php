<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Tambah - Data Keanggotaan') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('keanggotaan.store') }}" method="post" enctype="multipart/form-data">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nomor Keanggotaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_identitas" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"> 
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                <option value="1">Laki - Laki</option>
                                <option value="2">Wanita</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Pribadi <span class="text-red-500">*</span></label>
                        <textarea name="alamat_pribadi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
                    </div>
                    <hr>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama Institusi / Lembaga <span class="text-red-500">*</span></label>
                        <input type="text" name="institusi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Institusi <span class="text-red-500">*</span></label>
                        <textarea name="alamat_institusi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
                    </div>

                    <div class="mb-4">
                        <button class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</x-app-layout>
