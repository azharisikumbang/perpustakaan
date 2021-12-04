<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
            {{ __('Pengaturan') }}
        </h2>
    </x-slot>

    <div class="mb-8">
        <div class="mb-2">
            <div class="text-sm font-normal text-gray-500">ISBN</div>
            <div class="text-base font-semibold text-gray-900">{{ $nominal_denda }}</div>
        </div>
    </div>
</x-app-layout>
