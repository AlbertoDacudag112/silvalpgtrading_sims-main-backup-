@props(['color' => 'gray', 'label'])

@php
    $styles = [
        'green'  => 'bg-status-success-bg text-status-success',
        'yellow' => 'bg-status-warning-bg text-status-warning',
        'red'    => 'bg-status-danger-bg text-status-danger',
        'gray'   => 'bg-gray-100 text-gray-600',
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $styles[$color] ?? $styles['gray'] }}">
    {{ $label }}
</span>
