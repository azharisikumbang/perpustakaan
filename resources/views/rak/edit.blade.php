<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Edit - Data Rak') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <form action="{{ route('rak.update', [ 'rak' => $rak['id'] ]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Kode Rak <span class="text-red-500">*</span></label>
                <input type="text" name="kode" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('kode') ?? $rak['kode'] }}">
            </div>
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-900 block mb-2">Alias / Nama Rak</label>
                <input type="text" name="alias" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('alias') ?? $rak['alias'] }}">
            </div>
            <div class="mb-4">
                <button class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-200 font-medium inline-flex items-center rounded-lg text-sm px-3 py-2 text-center sm:ml-auto">
                    Perbaharui
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
