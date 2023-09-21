<button
    name="{{ $name }}"
    value="{{ $value }}"
    type="submit"
    class="w-full rounded-lg bg-gray-800 px-5 py-2.5 text-center text-sm font-medium uppercase tracking-widest text-white duration-200 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-gray-200 sm:w-auto">
    {{ $slot }}
</button>
