<li class="mx-2">
    @if ($linkname == $selectedlink)
    <p class="inline-block border-b-4 border-gray-800 px-2 pb-2 text-black dark:border-gray-300 dark:text-gray-200">
        {{ $slot }}
    </p>
    @else
    <a
        href="{{ route($linkname, ['bookId' => $bookId]) }}"
        class="inline-block border-b-4 border-transparent px-2 pb-2 text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300">
        {{ $slot }}
    </a>
    @endif
</li>
