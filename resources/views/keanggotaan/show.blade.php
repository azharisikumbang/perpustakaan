<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Detail - Keanggotaan') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="mb-4 grid grid-cols-4 gap-4">
                <div class="w-full">
                   <img src="{{ asset('images/placeholder/avatar.png') }}" class="w-full max-w-full rounded-lg" />
                </div>
                <div class="col-span-3 grid grid-cols-2 gap-4">
                    <!-- Pribadi -->
                    <div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">Nama</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['nama'] }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">Jenis Kelamin</div>
                            <div class="text-base font-semibold text-gray-900">{{ ($keanggotaan['nama'] == 1) ? 'Laki - laki' : 'Perempuan' }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">No. Telepon</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['kontak'] }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">Alamat</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['alamat_pribadi'] }}</div>
                        </div>
                    </div>
                    <!-- Institusi -->
                    <div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">No. Keanggotaan</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['nomor_identitas'] }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">Institusi</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['institusi'] }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-sm font-normal text-gray-500">Alamat Institusi</div>
                            <div class="text-base font-semibold text-gray-900">{{ $keanggotaan['alamat_institusi'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
