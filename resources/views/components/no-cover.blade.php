@props(['size'])

@php
	$size = $size ?? 'medium';
@endphp

<img src="{{ asset('images/placeholder/no-cover-book-' . $size . '.jpg') }}" class="w-full max-w-full" />