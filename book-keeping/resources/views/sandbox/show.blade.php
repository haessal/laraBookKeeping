<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sandbox') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Date picker') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Try date picker of flowbit.") }}
                    </p>
                    <form method="POST" action="{{ route('sandbox.show') }}">
                        @csrf
                        <div class="mt-4">
                        <input
                                datepicker
                                datepicker-autohide
                                datepicker-format="yyyy-mm-dd"
                                type="text"
                                id="sandbox-date"
                                name="sandbox-date"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Select date"
                                readonly />
                        </div>
                    </form>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Live counter') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Try counter component of LiveWire.") }}
                    </p>
                    <form method="POST" action="{{ route('sandbox.show') }}">
                        @csrf
                        <div class="mt-4">
                        <input
                                datepicker
                                datepicker-autohide
                                datepicker-format="yyyy-mm-dd"
                                type="text"
                                id="sandbox-date"
                                name="sandbox-date"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Select date"
                                readonly />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
