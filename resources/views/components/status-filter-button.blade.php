@props(['active' => false, 'href' => '#'])

<a 
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
        'bg-indigo-600 text-white border-transparent' => $active,
        'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' => !$active,
    ]) }}
>
    {{ $slot }}
</a>