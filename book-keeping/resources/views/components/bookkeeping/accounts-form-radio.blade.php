<div class="mb-2 mr-0 flex items-center sm:mb-0 sm:mr-6">
    <input
        id="{{ $id }}"
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        class="h-4 w-4 border-gray-300 bg-gray-100 focus:ring-0 dark:border-gray-600 dark:bg-gray-700"
        {{
        $checked
        }} />
    <label for="{{ $id }}" class="ml-2 block text-sm font-medium text-black dark:text-gray-200">{{ $slot }}</label>
</div>
