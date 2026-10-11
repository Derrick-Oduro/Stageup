<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Notification center
            </h2>
            <span class="rounded-full bg-slate-900 px-3 py-1 text-sm font-medium text-white">
                {{ $unreadCount }} unread
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                @forelse ($notifications as $notification)
                    <div class="rounded-2xl border {{ $notification->read_at ? 'border-slate-200 bg-white' : 'border-cyan-200 bg-cyan-50' }} p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] {{ $notification->read_at ? 'text-slate-500' : 'text-cyan-700' }}">
                                    {{ $notification->data['title'] ?? 'Game Street update' }}
                                </p>
                                <p class="mt-2 text-base text-slate-700">
                                    {{ $notification->data['message'] ?? 'You have a new update.' }}
                                </p>
                            </div>

                            @if (is_null($notification->read_at))
                                <form method="POST" action="{{ route('notifications.read', ['id' => $notification->id]) }}">
                                    @csrf
                                    <button type="submit" class="rounded-full bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-slate-700">
                                        Mark read
                                    </button>
                                </form>
                            @endif
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-600">
                        No notifications yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
