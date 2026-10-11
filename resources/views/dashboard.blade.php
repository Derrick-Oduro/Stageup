<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            {{ __("You're logged in!") }}
                        </div>
                        <a href="{{ route('notifications.index') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                            View notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
