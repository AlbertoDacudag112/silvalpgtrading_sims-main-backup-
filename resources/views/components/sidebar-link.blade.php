@props(['href', 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition ' .
        ($active
            ? 'bg-maroon-600 text-white shadow-sm'
            : 'text-gray-200 hover:bg-maroon-700/60 hover:text-white')
   ]) }}>
    {{ $slot }}
</a>