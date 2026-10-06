@props(['label', 'value', 'accent' => 'maroon'])

@php
    $accents = [
        'maroon' => 'bg-maroon-50 text-maroon-600',
        'green'  => 'bg-status-success-bg text-status-success',
        'yellow' => 'bg-status-warning-bg text-status-warning',
        'red'    => 'bg-status-danger-bg text-status-danger',
    ];
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $accents[$accent] ?? $accents['maroon'] }}">
        {{ $slot }}
    </div>
    <div class="min-w-0">
        <p class="text-xs text-gray-500">{{ $label }}</p>
        <p class="text-xl font-semibold text-gray-900 truncate">{{ $value }}</p>
    </div>
</div>
