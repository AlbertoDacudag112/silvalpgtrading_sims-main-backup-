@props(['color' => 'gray', 'label'])

@php
$colorMap = [
'green' => 'bg-status-success-bg text-status-success',
'yellow' => 'bg-status-warning-bg text-status-warning',
'red' => 'bg-status-danger-bg text-status-danger',
'gray' => 'bg-gray-100 text-gray-600',
];
$classes = $colorMap[$color] ?? $colorMap['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium $classes"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $color === 'green' ? 'bg-status-success' : ($color === 'yellow' ? 'bg-status-warning' : ($color === 'red' ? 'bg-status-danger' : 'bg-gray-400')) }}"></span>
    {{ $label }}
</span>