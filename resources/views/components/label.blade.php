@props(['value'])

<label {{ $attributes->merge(['class' => 'text-sm font-medium text-gray-900 block mb-2']) }}>
    {{ $value ?? $slot }}
</label>