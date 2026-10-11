<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tournaments | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span><div><div class="text-lg font-semibold tracking-tight">Game Street</div><div class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Community & fixtures</div></div></a>
                <nav class="flex flex-wrap items-center justify-end gap-2 text-sm text-slate-300">
                    <a href="{{ route('tournaments.index') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950">Tournaments</a>
                    <a href="{{ route('fixtures.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Fixtures</a>
                    <a href="{{ route('table.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Table</a>
                    <a href="{{ route('community.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Community</a>
                    @auth<a href="{{ route('dashboard') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Dashboard</a>@else<a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Log in</a>@if (Route::has('register'))<a href="{{ route('register') }}" class="rounded-full bg-white px-3 py-2 font-semibold text-slate-950 hover:bg-slate-200">Register</a>@endif @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-2 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">The competition floor</p>
                    <h1 class="mt-3 text-4xl font-black tracking-tight text-white">Tournaments</h1>
                    <p class="mt-3 max-w-2xl text-slate-400">Find the active rooms, follow the action, and join the next Game Street matchday.</p>
                </div>
                @auth
                    @if (auth()->user()->hasAnyRole(['organizer', 'admin']))
                        <a href="{{ route('admin.tournaments') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:border-cyan-400 hover:text-cyan-300">Manage tournaments</a>
                    @endif
                @endauth
            </div>

            <section class="mb-8 rounded-3xl border border-slate-800 bg-slate-900 p-5">
                <form method="GET" action="{{ route('tournaments.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="q" class="mb-1 block text-sm text-slate-300">Search</label>
                        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Tournament name or game" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-slate-100 placeholder:text-slate-600 focus:border-cyan-400 focus:ring-cyan-400" />
                    </div>
                    <div>
                        <label for="game" class="mb-1 block text-sm text-slate-300">Game</label>
                        <select id="game" name="game" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-slate-100 focus:border-cyan-400 focus:ring-cyan-400">
                            <option value="">All games</option>
                            @foreach ($games as $game)
                                <option value="{{ $game }}" @selected(($filters['game'] ?? null) === $game)>{{ $game }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="mb-1 block text-sm text-slate-300">Status</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-slate-100 focus:border-cyan-400 focus:ring-cyan-400">
                            <option value="">Any status</option>
                            <option value="registration_open" @selected(($filters['status'] ?? null) === 'registration_open')>Registration Open</option>
                            <option value="upcoming" @selected(($filters['status'] ?? null) === 'upcoming')>Upcoming</option>
                            <option value="in_progress" @selected(($filters['status'] ?? null) === 'in_progress')>In Progress</option>
                            <option value="completed" @selected(($filters['status'] ?? null) === 'completed')>Completed</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-1">
                        <a href="{{ route('tournaments.index') }}" class="rounded-xl border border-slate-700 px-3 py-2 text-sm font-medium text-slate-300 hover:border-cyan-400 hover:text-white">Reset</a>
                        <button type="submit" class="rounded-xl bg-cyan-500 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-400">Apply filters</button>
                    </div>
                </form>
            </section>

            @if ($tournaments->isEmpty())
                <section class="rounded-3xl border border-dashed border-slate-700 bg-slate-900 p-10 text-center">
                    <h2 class="text-lg font-semibold text-white">No tournaments match your filters</h2>
                    <p class="mt-2 text-sm text-slate-400">Try removing a filter or changing your search term.</p>
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
                        <article class="flex h-full flex-col rounded-3xl border border-slate-800 bg-slate-900 p-5 transition hover:-translate-y-0.5 hover:border-cyan-500/50">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-300">
                                    {{ str_replace('_', ' ', $tournament->status) }}
                                </span>
                                <span class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ str_replace('_', ' ', $tournament->format) }}</span>
                            </div>

                            <h3 class="text-2xl font-bold leading-tight text-white">{{ $tournament->name }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ $tournament->game }}</p>

                            <dl class="mt-5 space-y-3 text-sm text-slate-300">
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

                            <p class="mt-5 text-xs text-slate-500">Organized by {{ $tournament->organizer->name }}</p>

                            <div class="mt-auto grid grid-cols-3 gap-2 pt-5">
                                <a href="{{ route('tournaments.show', $tournament) }}" class="rounded-xl border border-slate-700 px-2 py-2 text-center text-xs font-semibold text-slate-200 hover:border-cyan-400 hover:text-cyan-300">View</a>

                                @auth
                                    @if ($isFollowed)
                                        <form method="POST" action="{{ route('tournaments.unfollow', $tournament) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-xl border border-slate-700 px-2 py-2 text-xs font-semibold text-slate-300 hover:border-cyan-400">Unfollow</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('tournaments.follow', $tournament) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-xl border border-slate-700 px-2 py-2 text-xs font-semibold text-slate-300 hover:border-cyan-400">Follow</button>
                                        </form>
                                    @endif

                                    @if ($isJoined)
                                        <form method="POST" action="{{ route('tournaments.leave', $tournament) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-xl bg-slate-700 px-2 py-2 text-xs font-semibold text-white hover:bg-slate-600">Leave</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('tournaments.join', $tournament) }}">
                                            @csrf
                                            <button type="submit" @disabled(! $canJoin) class="w-full rounded-xl bg-cyan-500 px-2 py-2 text-xs font-semibold text-slate-950 enabled:hover:bg-cyan-400 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-500">Join</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-700 px-2 py-2 text-center text-xs font-semibold text-slate-300 hover:border-cyan-400">Follow</a>
                                    <a href="{{ route('login') }}" class="rounded-xl bg-cyan-500 px-2 py-2 text-center text-xs font-semibold text-slate-950 hover:bg-cyan-400">Join</a>
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
