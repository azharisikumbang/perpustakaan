<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Perbaharui - Data Akun') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('akun.update') }}" method="post" enctype="multipart/form-data">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    @method('PUT')
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nomor Keanggotaan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('kode') ?? $anggota['kode'] }}" readonly="">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('nama') ?? $anggota['nama'] }}">
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('kontak') ?? $anggota['kontak'] }}"> 
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                <option value="1" <?= ((old('jenis_kelamin') ?? $anggota['jenis_kelamin']) == '1' ? 'selected' : '')  ?>>Laki - Laki</option>
                                <option value="2" <?= ((old('jenis_kelamin') ?? $anggota['jenis_kelamin']) == '2' ? 'selected' : '')  ?>>Wanita</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Pribadi <span class="text-red-500">*</span></label>
                        <textarea name="alamat_pribadi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">{{ old('alamat_pribadi') ?? $anggota['alamat_pribadi'] }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama Institusi / Lembaga <span class="text-red-500">*</span></label>
                        <select name="institusi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                        <?php $listInstansi = ['MTSN', 'MTSS', 'MAN', 'UIN', 'IAIN', 'Kementrian Agama', 'Kementrian Balai Pendidikan'];
                            foreach ($listInstansi as $instansi) { ?>
                                <option value="{{ $instansi }}" <?= (strtolower($anggota['institusi']) == strtolower($instansi)) ? 'selected' : '' ?>>{{ $instansi }}</option> 
                        <?php } ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Institusi <span class="text-red-500">*</span></label>
                        <textarea name="alamat_institusi" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">{{ old('alamat_institusi') ?? $anggota['alamat_institusi'] }}</textarea>
                    </div>

                    <div class="mb-4">
                        <button class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                            Perbaharui
                        </button>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</x-app-layout>
