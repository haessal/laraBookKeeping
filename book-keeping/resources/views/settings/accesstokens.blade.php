<x-app-layout>
    <x-slot name="title">{{ __('Personal access tokens') }}</x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-6 md:px-8">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Personal access tokens') }}</h2>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white p-6">
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <x-primary-button type="submit">{{ __('Generate new token') }}</x-primary-button>
                    </form>
                    <br />
                    <form method="POST" action="{{ route('settings_tokens') }}">
                        @csrf
                        <input name="_method" type="hidden" value="DELETE" />
                        <x-primary-button type="submit">{{ __('Delete token') }}</x-primary-button>
                    </form>
                    <br />
                    @if ($token != null) @if ($message_for_new_token != null) {{ $message_for_new_token }}
                    <br />
                    @endif {{ $token }} @else @if ($message_for_no_token != null) {{ $message_for_no_token }}
                    <br />
                    @else {{ $timestamp }}
                    <br />
                    @endif @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
