<label for="{{ $id }}" class="p-1 text-black dark:text-gray-200">{{ $title }}</label>
<select
    id="{{ $id }}"
    name="{{ $name }}"
    class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400 sm:w-1/2">
    {{ $slot }}
</select>
