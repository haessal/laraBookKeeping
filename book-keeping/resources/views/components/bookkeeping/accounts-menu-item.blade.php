@if ($accountsmenuname == $selectedaccountsmenu)
<li class="mx-1 rounded-lg border border-gray-800 p-2 text-black dark:border-gray-300 dark:text-gray-200">
    <p>{{ $slot }}</p>
</li>
@else
<li
    class="mx-1 rounded-lg border border-transparent p-2 text-gray-500 hover:border hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300">
    <a
        class="focus:border-gray-900 focus:ring-gray-900 dark:focus:border-gray-400 dark:focus:ring-gray-400"
        href="{{ route($linkname, ['bookId' => $bookId]) }}">
        {{ $slot }}
    </a>
</li>
@endif
