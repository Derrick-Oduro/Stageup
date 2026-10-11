<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $tournament->name }} | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span><div><div class="text-lg font-semibold">Game Street</div><div class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Community & fixtures</div></div></a>
                <nav class="flex flex-wrap justify-end gap-2 text-sm text-slate-300"><a href="{{ route('tournaments.index') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950">Tournaments</a><a href="{{ route('fixtures.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Fixtures</a><a href="{{ route('table.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Table</a><a href="{{ route('community.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Community</a>@auth<a href="{{ route('dashboard') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Dashboard</a>@else<a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Log in</a>@endauth</nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('tournaments.index') }}" class="text-sm text-slate-400 hover:text-cyan-300">← Back to tournaments</a>
            @php
                $participantCount = $tournament->real_participants_count ?? 0;
                $followerCount = $tournament->real_followers_count ?? 0;
                $canJoin = $tournament->status === 'registration_open'
                    && (! $tournament->registration_deadline || now()->lte($tournament->registration_deadline))
                    && ($participantCount < $tournament->max_participants);
            @endphp
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 lg:col-span-2">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-300">
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </span>
                        <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            {{ str_replace('_', ' ', $tournament->format) }}
                        </span>
                        <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            {{ $tournament->game }}
                        </span>
                    </div>

                    <h1 class="text-4xl font-black tracking-tight text-white">{{ $tournament->name }}</h1>
                    <p class="mt-2 text-sm text-slate-400">Hosted by {{ $tournament->organizer->name }}</p>

                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Starts</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ optional($tournament->starts_at)->format('M d, Y H:i') ?? 'TBD' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Registration Deadline</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ optional($tournament->registration_deadline)->format('M d, Y H:i') ?? 'TBD' }}</p>
                        </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Participants</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ $participantCount }} / {{ $tournament->max_participants }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Followers</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ $followerCount }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-xl font-bold text-white">Rules</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-300">{{ $tournament->rules ?: 'Rules will be published by the organizer soon.' }}</p>
                    </div>
                </section>

                <aside class="rounded-3xl border border-slate-800 bg-slate-900 p-5">
                    <h2 class="text-xl font-bold text-white">Join this room</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">Follow this tournament for updates or register if open.</p>

                    <div class="mt-4 space-y-2">
                        @auth
                            @if ($isFollowed)
                                <form method="POST" action="{{ route('tournaments.unfollow', $tournament) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-xl border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 hover:border-cyan-400">Unfollow tournament</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tournaments.follow', $tournament) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 hover:border-cyan-400">Follow tournament</button>
                                </form>
                            @endif

                            @if ($isJoined)
                                <form method="POST" action="{{ route('tournaments.leave', $tournament) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-xl bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-600">Leave tournament</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tournaments.join', $tournament) }}">
                                    @csrf
                                    <button type="submit" @disabled(! $canJoin) class="w-full rounded-xl bg-cyan-500 px-3 py-2 text-sm font-semibold text-slate-950 enabled:hover:bg-cyan-400 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-500">Join as participant</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full rounded-xl border border-slate-700 px-3 py-2 text-center text-sm font-semibold text-slate-200 hover:border-cyan-400">Log in to follow</a>
                            <a href="{{ route('login') }}" class="block w-full rounded-xl bg-cyan-500 px-3 py-2 text-center text-sm font-semibold text-slate-950 hover:bg-cyan-400">Log in to join</a>
                        @endauth
                    </div>

                    <div class="mt-6 rounded-2xl border border-dashed border-slate-700 bg-slate-950 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-300">Community links</p>
                        <div class="mt-3 space-y-2 text-sm"><a href="{{ route('fixtures.index') }}" class="block text-slate-300 hover:text-cyan-300">View all fixtures →</a><a href="{{ route('table.index', ['tournament' => $tournament->slug]) }}" class="block text-slate-300 hover:text-cyan-300">View league table →</a><a href="{{ route('community.index') }}" class="block text-slate-300 hover:text-cyan-300">Open community room →</a></div>
                    </div>
                </aside>
            </div>

            <section class="mt-6 rounded-3xl border border-slate-800 bg-slate-900 p-6">
                <div class="flex items-end justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">Matchday</p><h2 class="mt-2 text-2xl font-bold text-white">Tournament fixtures</h2></div><a href="{{ route('fixtures.index') }}" class="text-sm font-semibold text-slate-400 hover:text-cyan-300">All fixtures →</a></div>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @forelse ($tournament->fixtures as $fixture)
                        @php($result = $fixture->officialResult)
                        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4"><div class="flex items-center justify-between"><span class="text-xs uppercase tracking-[0.16em] text-slate-500">{{ $fixture->round_name ?: 'Fixture' }}</span><span class="text-xs text-slate-500">{{ str_replace('_', ' ', $fixture->status) }}</span></div><div class="mt-4 flex items-center justify-between gap-3 text-sm font-semibold text-white"><span>{{ $result?->winner?->name ?? $fixture->participants->first()?->name ?? 'TBD' }}</span><span class="text-cyan-300">{{ $result ? $result->score_for.' - '.$result->score_against : 'vs' }}</span><span>{{ $result?->loser?->name ?? $fixture->participants->last()?->name ?? 'TBD' }}</span></div></div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-700 p-6 text-sm text-slate-400 md:col-span-2">Fixtures will appear here when the admin schedules the matchday.</div>
                    @endforelse
                </div>
            </section>
        </main>
    </body>
</html>
