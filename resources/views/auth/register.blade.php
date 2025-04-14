<x-guest-layout>
    <x-auth-card class="max-w-5xl sm:max-w-5xl">
        <x-slot name="logo">
            <a href="/">
                <div class="flex justify-between items-center">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    <div class="pl-4 text-xl w-80 font-bold text-white">PERPUSTAKAAN BALAI PENDIDIKAN DAN PELATIHAN
                        KEAGAMAAN PADANG</div>
                </div>
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="mb-4 font-bold">Identitas Anda</div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">No. Telepon <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="kontak"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-900 block mb-2">Jenis Kelamin <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_kelamin"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                                <option value="1">Laki - Laki</option>
                                <option value="2">Wanita</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Pribadi <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat_pribadi"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nomor Keanggotaan
                            (NISN/NIDN/dll)<span class="text-red-500">*</span></label>
                        <input type="text" name="kode"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Nama Institusi / Lembaga <span
                                class="text-red-500">*</span></label>
                        <select name="institusi"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                            <?php $listInstansi = ['MTSN', 'MTSS', 'MAN', 'UIN', 'IAIN', 'Kementrian Agama', 'Kementrian Balai Pendidikan'];
                            foreach ($listInstansi as $instansi) { ?>
                            <option value="{{ $instansi }}">{{ $instansi }}</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-900 block mb-2">Alamat Institusi <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat_institusi"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
                    </div>
                </div>
                <div>
                    <div class="mb-4 font-bold">Data Akun Anda</div>
                    <!-- Name -->
                    <!--  <div>
                        <x-label for="name" :value="__('Name')" />

                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    </div> -->

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-label for="email" :value="__('Email')" />

                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                            required />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-label for="password" :value="__('Password')" />

                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autocomplete="new-password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-label for="password_confirmation" :value="__('Confirm Password')" />

                        <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required />
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Sudah Terdaftar ?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Daftarkan') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>