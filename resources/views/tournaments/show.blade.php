<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $tournament->name }} | {{ config('app.name', 'TourneyHub') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('tournaments.index') }}" class="text-xl font-semibold tracking-tight">TourneyHub</a>
                <nav class="flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('tournaments.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium hover:bg-gray-50">All Tournaments</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium hover:bg-gray-50">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-50">Log in</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @php
                $participantCount = $tournament->real_participants_count ?? 0;
                $followerCount = $tournament->real_followers_count ?? 0;
                $canJoin = $tournament->status === 'registration_open'
                    && (! $tournament->registration_deadline || now()->lte($tournament->registration_deadline))
                    && ($participantCount < $tournament->max_participants);
            @endphp
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <section class="rounded-xl border border-gray-200 bg-white p-5 lg:col-span-2">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                            {{ str_replace('_', ' ', $tournament->format) }}
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                            {{ $tournament->game }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-semibold tracking-tight">{{ $tournament->name }}</h1>
                    <p class="mt-2 text-sm text-gray-600">Hosted by {{ $tournament->organizer->name }}</p>

                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Starts</p>
                            <p class="mt-1 text-sm font-semibold">{{ optional($tournament->starts_at)->format('M d, Y H:i') ?? 'TBD' }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Registration Deadline</p>
                            <p class="mt-1 text-sm font-semibold">{{ optional($tournament->registration_deadline)->format('M d, Y H:i') ?? 'TBD' }}</p>
                        </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Participants</p>
                                <p class="mt-1 text-sm font-semibold">{{ $participantCount }} / {{ $tournament->max_participants }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Followers</p>
                                <p class="mt-1 text-sm font-semibold">{{ $followerCount }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg font-semibold">Rules</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $tournament->rules ?: 'Rules will be published by the organizer soon.' }}</p>
                    </div>
                </section>

                <aside class="rounded-xl border border-gray-200 bg-white p-5">
                    <h2 class="text-lg font-semibold">Actions</h2>
                    <p class="mt-1 text-sm text-gray-600">Follow this tournament for updates or register if open.</p>

                    <div class="mt-4 space-y-2">
                        @auth
                            @if ($isFollowed)
                                <form method="POST" action="{{ route('tournaments.unfollow', $tournament) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Unfollow Tournament</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tournaments.follow', $tournament) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Follow Tournament</button>
                                </form>
                            @endif

                            @if ($isJoined)
                                <form method="POST" action="{{ route('tournaments.leave', $tournament) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-md bg-gray-700 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-600">Leave Tournament</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tournaments.join', $tournament) }}">
                                    @csrf
                                    <button type="submit" @disabled(! $canJoin) class="w-full rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white enabled:hover:bg-gray-700 disabled:cursor-not-allowed disabled:bg-gray-300">Join as Participant</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-center text-sm font-semibold hover:bg-gray-50">Log in to Follow</a>
                            <a href="{{ route('login') }}" class="block w-full rounded-md bg-gray-900 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-gray-700">Log in to Join</a>
                        @endauth
                    </div>

                    <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Next</p>
                        <p class="mt-1 text-sm text-gray-700">Bracket visualization and match schedule will appear here in the next iteration.</p>
                    </div>
                </aside>
            </div>
        </main>
    </body>
</html>
