@php
    $currentSort = request('sort');
    $currentDirection = request('direction');
    $isActive = $currentSort === $field;
@endphp

<span class="inline-flex flex-col ml-1">
    <svg class="w-2.5 h-2.5 {{ $isActive && $currentDirection === 'asc' ? 'text-green-600' : 'text-gray-300' }} group-hover:text-green-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
    </svg>
    <svg class="w-2.5 h-2.5 -mt-1 {{ $isActive && $currentDirection === 'desc' ? 'text-green-600' : 'text-gray-300' }} group-hover:text-green-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
    </svg>
</span>
