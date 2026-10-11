<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Game Street') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span>
                    <div>
                        <div class="text-lg font-semibold tracking-tight">Game Street</div>
                        <div class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Community & fixtures</div>
                    </div>
                </a>

                <nav class="flex items-center gap-3 text-sm text-slate-300">
                    <a href="{{ route('tournaments.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Tournaments</a>
                    <a href="{{ route('fixtures.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Fixtures</a>
                    <a href="{{ route('table.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Table</a>
                    <a href="{{ route('community.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Community</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950 hover:bg-cyan-400">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-full bg-white px-3 py-2 font-semibold text-slate-950 hover:bg-slate-200">Register</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden">
                <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8 lg:py-24">
                    <div class="flex flex-col justify-center">
                        <span class="mb-4 inline-flex w-fit rounded-full border border-cyan-500/40 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">The gaming community hub</span>
                        <h1 class="max-w-xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">Where the community plays, chats, and follows every fixture.</h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Game Street is where players come together, admins run tournaments, fixtures are tracked live, and the whole room stays connected through chat, standings, and notifications.</p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-500 px-6 py-3 text-base font-semibold text-slate-950 hover:bg-cyan-400">View fixtures</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 px-6 py-3 text-base font-semibold text-white hover:border-cyan-400 hover:text-cyan-300">Join the community</a>
                            @endif
                        </div>

                        <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 text-left">
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Active</dt>
                                <dd class="mt-2 text-2xl font-bold text-white">{{ \App\Models\Tournament::count() }}</dd>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Players</dt>
                                <dd class="mt-2 text-2xl font-bold text-white">{{ \App\Models\User::count() }}</dd>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                                <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Chat</dt>
                                <dd class="mt-2 text-2xl font-bold text-cyan-300">Live</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-5 shadow-2xl shadow-cyan-950/30">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Live fixture</p>
                                <h2 class="mt-2 text-2xl font-bold text-white">Night League</h2>
                            </div>
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">On now</span>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <div>
                                    <div class="text-sm text-slate-400">Fixture</div>
                                    <div class="font-semibold text-white">GriX vs Beacon</div>
                                </div>
                                <div class="text-2xl font-black text-cyan-300">2 - 1</div>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <div>
                                    <div class="text-sm text-slate-400">Next</div>
                                    <div class="font-semibold text-white">Velo FC vs Tempest</div>
                                </div>
                                <div class="text-sm font-medium text-slate-300">19:30</div>
                            </div>

                            <div id="table" class="rounded-2xl border border-dashed border-slate-700 bg-slate-950/60 p-4">
                                <div class="flex items-center justify-between text-sm text-slate-300">
                                    <span>Table</span>
                                    <span>Top 5</span>
                                </div>
                                <div class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-center justify-between"><span class="text-slate-300">1. GriX</span><span class="font-semibold text-white">18 pts</span></div>
                                    <div class="flex items-center justify-between"><span class="text-slate-300">2. Beacon</span><span class="font-semibold text-white">15 pts</span></div>
                                    <div class="flex items-center justify-between"><span class="text-slate-300">3. Tempest</span><span class="font-semibold text-white">12 pts</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fixtures" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
                <div class="mb-8 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">What the community gets</p>
                    <h2 class="mt-3 text-3xl font-bold text-white">Fixtures, tables, and updates in one place</h2>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6">
                        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-500/15 text-xl text-cyan-300">🏟️</div>
                        <h3 class="text-xl font-semibold text-white">Fixture board</h3>
                        <p class="mt-3 text-slate-300">Admins manage the match flow while players keep track of upcoming fixtures and recent scorelines.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6">
                        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-500/15 text-xl text-violet-300">📊</div>
                        <h3 class="text-xl font-semibold text-white">League table</h3>
                        <p class="mt-3 text-slate-300">Every result feeds the standings so the community always knows where the pressure is.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6">
                        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/15 text-xl text-emerald-300">💬</div>
                        <h3 class="text-xl font-semibold text-white">Community chat</h3>
                        <p class="mt-3 text-slate-300">The app becomes a real social hub for match talk, tournament updates, and daily community energy.</p>
                    </div>
                </div>
            </section>

            <section id="community" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">Featured community</p>
                        <h2 class="mt-3 text-3xl font-bold text-white">Tournament rooms and community updates</h2>
                    </div>
                    <a href="{{ route('tournaments.index') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:border-cyan-400 hover:text-cyan-300">Open tournaments</a>
                </div>

                <div class="grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
                    <div class="grid gap-5">
                        @forelse ($featuredTournaments as $tournament)
                            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 transition hover:-translate-y-0.5 hover:border-cyan-500/40">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <span class="rounded-full border border-slate-700 bg-slate-950 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-300">{{ str_replace('_', ' ', $tournament->status) }}</span>
                                    <span class="text-xs uppercase tracking-[0.22em] text-slate-400">{{ $tournament->format }}</span>
                                </div>

                                <h3 class="text-2xl font-bold text-white">{{ $tournament->name }}</h3>
                                <p class="mt-2 text-sm text-slate-400">{{ $tournament->game }}</p>

                                <dl class="mt-5 space-y-3 text-sm text-slate-300">
                                    <div class="flex items-center justify-between"><dt>Starts</dt><dd>{{ optional($tournament->starts_at)->format('M d, Y') ?? 'TBD' }}</dd></div>
                                    <div class="flex items-center justify-between"><dt>Participants</dt><dd>{{ $tournament->real_participants_count ?? 0 }}/{{ $tournament->max_participants }}</dd></div>
                                    <div class="flex items-center justify-between"><dt>Followers</dt><dd>{{ $tournament->real_followers_count ?? 0 }}</dd></div>
                                </dl>

                                <div class="mt-5 flex items-center justify-between border-t border-slate-800 pt-4">
                                    <span class="text-xs text-slate-400">Organized by {{ $tournament->organizer->name }}</span>
                                    <a href="{{ route('tournaments.show', $tournament) }}" class="rounded-full bg-cyan-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-cyan-400">View</a>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900 p-8 text-slate-300">
                                No community tournaments are live yet. The room is waiting for the next matchday.
                            </div>
                        @endforelse
                    </div>

                    <aside class="rounded-3xl border border-slate-800 bg-slate-900 p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">Community chat</h3>
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-emerald-300">Online</span>
                        </div>

                        <div class="space-y-3 text-sm text-slate-300">
                            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <p class="font-medium text-white">Jude</p>
                                <p class="mt-1">Fixture is locked in for tonight. Everyone in the chat should be ready.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <p class="font-medium text-white">Maya</p>
                                <p class="mt-1">Beacon are looking sharp this week. This table is heating up.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <p class="font-medium text-white">Kane</p>
                                <p class="mt-1">Admin has posted the updated bracket. We are live.</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </main>
    </body>
</html>
