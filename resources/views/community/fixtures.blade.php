<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fixtures | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span>
                    <span class="text-lg font-semibold">Game Street</span>
                </a>
                <nav class="flex flex-wrap items-center justify-end gap-2 text-sm text-slate-300">
                    <a href="{{ route('fixtures.index') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950">Fixtures</a>
                    <a href="{{ route('table.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Table</a>
                    <a href="{{ route('community.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Community</a>
                    <a href="{{ route('tournaments.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Tournaments</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">Match centre</p>
                <h1 class="mt-3 text-4xl font-black text-white">Fixtures</h1>
                <p class="mt-3 max-w-2xl text-slate-400">Every scheduled match, submitted score, and verified result from the Game Street community.</p>
            </div>

            <section class="grid gap-5 lg:grid-cols-2">
                @forelse ($fixtures as $fixture)
                    @php($result = $fixture->officialResult)
                    <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-cyan-300">{{ $fixture->tournament->name }}</p>
                                <h2 class="mt-2 text-xl font-bold text-white">{{ $fixture->round_name ?: 'Community fixture' }}</h2>
                            </div>
                            <span class="rounded-full border border-slate-700 bg-slate-950 px-3 py-1 text-xs uppercase tracking-[0.15em] text-slate-300">{{ str_replace('_', ' ', $fixture->status) }}</span>
                        </div>

                        <div class="mt-6 grid grid-cols-[1fr_auto_1fr] items-center gap-3 text-center">
                            <span class="text-lg font-semibold text-white">{{ $result?->winner?->name ?? $fixture->participants->first()?->name ?? 'TBD' }}</span>
                            <span class="text-2xl font-black text-cyan-300">{{ $result ? $result->score_for.' - '.$result->score_against : 'vs' }}</span>
                            <span class="text-lg font-semibold text-white">{{ $result?->loser?->name ?? $fixture->participants->last()?->name ?? 'TBD' }}</span>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-slate-800 pt-4 text-sm text-slate-400">
                            <span>{{ optional($fixture->scheduled_at)->format('M d, Y · H:i') ?? 'Schedule pending' }}</span>
                            <span>{{ $fixture->participants->count() }} players</span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900 p-10 text-slate-300 lg:col-span-2">No public fixtures have been scheduled yet.</div>
                @endforelse
            </section>

            <div class="mt-8">{{ $fixtures->links() }}</div>
        </main>
    </body>
</html>