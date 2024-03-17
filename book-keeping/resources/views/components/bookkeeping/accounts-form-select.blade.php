<div class="mx-3 mt-3 mb-6">
    <x-bookkeeping.accounts-form-label for="{{ $id }}">{{ $title }}</x-bookkeeping.accounts-form-label>
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400">
        {{ $slot }}
    </select>
</div>
