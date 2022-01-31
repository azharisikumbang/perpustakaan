<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="mb-8">
    	<p>Selamat datang {{ auth()->user()->name }}.</p>
    </div>
</x-app-layout>
