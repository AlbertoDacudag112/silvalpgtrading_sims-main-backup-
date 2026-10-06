@props(['label', 'value', 'icon' => null, 'accent' => 'maroon'])

@php
$accentMap = [
'maroon' => 'bg-maroon-50 text-maroon-600',
'green' => 'bg-status-success-bg text-status-success',
'yellow' => 'bg-status-warning-bg text-status-warning',
'red' => 'bg-status-danger-bg text-status-danger',
];
$accentClasses = $accentMap[$accent] ?? $accentMap['maroon'];
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm">
    <div class="w-11 h-11 rounded-lg flex items-center justify-center {{ $accentClasses }}">
        {{ $icon }}
    </div>
    <div>
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="text-xl font-semibold text-gray-900">{{ $value }}</p>
    </div>
</div>