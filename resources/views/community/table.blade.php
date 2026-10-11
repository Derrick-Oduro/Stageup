<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Table | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span><span class="text-lg font-semibold">Game Street</span></a>
                <nav class="flex flex-wrap justify-end gap-2 text-sm text-slate-300"><a href="{{ route('fixtures.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Fixtures</a><a href="{{ route('table.index') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950">Table</a><a href="{{ route('community.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Community</a><a href="{{ route('tournaments.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Tournaments</a></nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">Standings</p><h1 class="mt-3 text-4xl font-black text-white">League table</h1><p class="mt-3 text-slate-400">Verified results become points, wins, and goal difference.</p></div>
                <form method="GET" action="{{ route('table.index') }}" class="flex items-center gap-2"><label for="tournament" class="sr-only">Tournament</label><select id="tournament" name="tournament" class="rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100"><option value="">Select tournament</option>@foreach ($tournaments as $option)<option value="{{ $option->slug }}" @selected($tournament?->id === $option->id)>{{ $option->name }}</option>@endforeach</select><button class="rounded-xl bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-400">View table</button></form>
            </div>

            <section class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900">
                <div class="border-b border-slate-800 px-5 py-4"><h2 class="font-semibold text-white">{{ $tournament?->name ?? 'No tournament selected' }}</h2></div>
                <div class="overflow-x-auto"><table class="w-full min-w-[680px] text-left text-sm"><thead class="bg-slate-950/70 text-xs uppercase tracking-[0.15em] text-slate-500"><tr><th class="px-5 py-4">#</th><th class="px-5 py-4">Player</th><th class="px-5 py-4">P</th><th class="px-5 py-4">W</th><th class="px-5 py-4">L</th><th class="px-5 py-4">GD</th><th class="px-5 py-4 text-cyan-300">Pts</th></tr></thead><tbody class="divide-y divide-slate-800">@forelse ($standings as $index => $row)<tr class="text-slate-300"><td class="px-5 py-4 font-semibold text-slate-500">{{ $index + 1 }}</td><td class="px-5 py-4 font-semibold text-white">{{ $row['player']->name }}</td><td class="px-5 py-4">{{ $row['played'] }}</td><td class="px-5 py-4 text-emerald-300">{{ $row['wins'] }}</td><td class="px-5 py-4">{{ $row['losses'] }}</td><td class="px-5 py-4">{{ $row['difference'] }}</td><td class="px-5 py-4 font-bold text-cyan-300">{{ $row['points'] }}</td></tr>@empty<tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">No verified results yet. The table will come alive after the first official result.</td></tr>@endforelse</tbody></table></div>
            </section>
        </main>
    </body>
</html>