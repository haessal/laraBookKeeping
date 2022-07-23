<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Personal access tokens') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <x-primary-button type="submit">
                            {{ __('Generate new token') }}
                        </x-primary-button>
                    </form>
                    <br>
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <input name="_method" type="hidden" value="DELETE">
                        <x-primary-button type="submit">
                            {{ __('Delete token') }}
                        </x-primary-button>
                    </form>
                    <br>
                    @if ($token != null)
                        @if ($message_for_new_token != null)
                            {{ $message_for_new_token }}
                            <br>
                        @endif
                        {{ $token }}
                    @else
                        @if ($message_for_no_token != null)
                            {{ $message_for_no_token }}
                            <br>
                        @else
                            {{ $timestamp }}
                            <br>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
