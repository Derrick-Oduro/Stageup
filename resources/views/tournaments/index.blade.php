<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tournaments | {{ config('app.name', 'TourneyHub') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('tournaments.index') }}" class="text-xl font-semibold tracking-tight">TourneyHub</a>
                <nav class="flex items-center gap-2 sm:gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium hover:bg-gray-50">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-50">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Register</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-2 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-wide text-gray-500">Discover</p>
                    <h1 class="text-3xl font-semibold tracking-tight">Public Tournaments</h1>
                    <p class="mt-1 text-sm text-gray-600">Follow tournaments as a fan or register as a participant when registration is open.</p>
                </div>
                @auth
                    @if (auth()->user()->hasAnyRole(['organizer', 'admin']))
                        <a href="#" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Create Tournament</a>
                    @endif
                @endauth
            </div>

            <section class="mb-6 rounded-xl border border-gray-200 bg-white p-4 sm:mb-8 sm:p-5">
                <form method="GET" action="{{ route('tournaments.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Search</label>
                        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Tournament name or game" class="w-full rounded-md border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500" />
                    </div>
                    <div>
                        <label for="game" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Game</label>
                        <select id="game" name="game" class="w-full rounded-md border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500">
                            <option value="">All games</option>
                            @foreach ($games as $game)
                                <option value="{{ $game }}" @selected(($filters['game'] ?? null) === $game)>{{ $game }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Status</label>
                        <select id="status" name="status" class="w-full rounded-md border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500">
                            <option value="">Any status</option>
                            <option value="registration_open" @selected(($filters['status'] ?? null) === 'registration_open')>Registration Open</option>
                            <option value="upcoming" @selected(($filters['status'] ?? null) === 'upcoming')>Upcoming</option>
                            <option value="in_progress" @selected(($filters['status'] ?? null) === 'in_progress')>In Progress</option>
                            <option value="completed" @selected(($filters['status'] ?? null) === 'completed')>Completed</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-1">
                        <a href="{{ route('tournaments.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium hover:bg-gray-50">Reset</a>
                        <button type="submit" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Apply Filters</button>
                    </div>
                </form>
            </section>

            @if ($tournaments->isEmpty())
                <section class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                    <h2 class="text-lg font-semibold">No tournaments match your filters</h2>
                    <p class="mt-1 text-sm text-gray-600">Try removing a filter or changing your search term.</p>
                </section>
            @else
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tournaments as $tournament)
                        @php
                            $isFollowed = (bool) ($tournament->is_followed ?? false);
                            $isJoined = (bool) ($tournament->is_joined ?? false);
                            $participantCount = $tournament->real_participants_count ?? 0;
                            $followerCount = $tournament->real_followers_count ?? 0;
                            $canJoin = $tournament->status === 'registration_open'
                                && (! $tournament->registration_deadline || now()->lte($tournament->registration_deadline))
                                && ($participantCount < $tournament->max_participants);
                        @endphp
                        <article class="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                                    {{ str_replace('_', ' ', $tournament->status) }}
                                </span>
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $tournament->format) }}</span>
                            </div>

                            <h3 class="text-lg font-semibold leading-tight">{{ $tournament->name }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $tournament->game }}</p>

                            <dl class="mt-4 space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between">
                                    <dt>Starts</dt>
                                    <dd class="font-medium">{{ optional($tournament->starts_at)->format('M d, Y') ?? 'TBD' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Participants</dt>
                                    <dd class="font-medium">{{ $participantCount }} / {{ $tournament->max_participants }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Followers</dt>
                                    <dd class="font-medium">{{ $followerCount }}</dd>
                                </div>
                            </dl>

                            <p class="mt-3 text-xs text-gray-500">Organized by {{ $tournament->organizer->name }}</p>

                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <a href="{{ route('tournaments.show', $tournament) }}" class="rounded-md border border-gray-300 px-2 py-2 text-center text-xs font-semibold hover:bg-gray-50">View</a>

                                @auth
                                    @if ($isFollowed)
                                        <form method="POST" action="{{ route('tournaments.unfollow', $tournament) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs font-semibold hover:bg-gray-50">Unfollow</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('tournaments.follow', $tournament) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs font-semibold hover:bg-gray-50">Follow</button>
                                        </form>
                                    @endif

                                    @if ($isJoined)
                                        <form method="POST" action="{{ route('tournaments.leave', $tournament) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-md bg-gray-700 px-2 py-2 text-xs font-semibold text-white hover:bg-gray-600">Leave</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('tournaments.join', $tournament) }}">
                                            @csrf
                                            <button type="submit" @disabled(! $canJoin) class="w-full rounded-md bg-gray-900 px-2 py-2 text-xs font-semibold text-white enabled:hover:bg-gray-700 disabled:cursor-not-allowed disabled:bg-gray-300">Join</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-2 py-2 text-center text-xs font-semibold hover:bg-gray-50">Follow</a>
                                    <a href="{{ route('login') }}" class="rounded-md bg-gray-900 px-2 py-2 text-center text-xs font-semibold text-white hover:bg-gray-700">Join</a>
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="mt-6">{{ $tournaments->links() }}</div>
            @endif
        </main>
    </body>
</html>
