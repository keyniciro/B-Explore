@props([
    'label',
    'name',
    'type' => 'text',
])

@php
    $base = 'w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500';
    $border = $errors->has($name) ? 'border-red-400' : 'border-gray-300';
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm text-gray-600 mb-1">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3" {{ $attributes->merge(['class' => "$base $border"])->except('value') }}>{{ $attributes->get('value') }}</textarea>
    @elseif ($type === 'file')
        <input id="{{ $name }}" name="{{ $name }}" type="file" {{ $attributes->merge(['class' => 'w-full text-sm text-gray-600']) }}>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->merge(['class' => "$base $border"]) }}>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
