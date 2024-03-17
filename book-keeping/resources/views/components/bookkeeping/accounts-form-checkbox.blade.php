<div class="mb-2 flex items-center">
    <input
        id="{{ $id }}"
        type="checkbox"
        name="{{ $name }}"
        value="1"
        class="ml-2 h-4 w-4 border-gray-300 bg-gray-100 focus:ring-0 dark:border-gray-600 dark:bg-gray-700"
        {{
        $checked
        }} />
    <label for="{{ $id }}" class="ml-2 block text-sm font-medium text-black dark:text-gray-200">{{ $slot }}</label>
</div>
