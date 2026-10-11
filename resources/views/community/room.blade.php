<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Community | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-lg font-bold text-cyan-300">G</span><div><div class="text-lg font-semibold">Game Street</div><div class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Community & fixtures</div></div></a>
                <nav class="flex flex-wrap justify-end gap-2 text-sm text-slate-300"><a href="{{ route('fixtures.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Fixtures</a><a href="{{ route('table.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Table</a><a href="{{ route('community.index') }}" class="rounded-full bg-cyan-500 px-3 py-2 font-semibold text-slate-950">Community</a><a href="{{ route('tournaments.index') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Tournaments</a>@auth<a href="{{ route('dashboard') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Dashboard</a>@else<a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-3 py-2 hover:border-cyan-400 hover:text-white">Log in</a>@endauth</nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">The room</p><h1 class="mt-3 text-4xl font-black text-white">Community</h1><p class="mt-3 max-w-2xl text-slate-400">Talk through matchdays, share updates, and keep up with the Game Street competition floor.</p></div>

            @if (session('status'))<div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">{{ $errors->first('body') }}</div>@endif

            <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
                <section id="chat" class="rounded-3xl border border-slate-800 bg-slate-900 p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4"><div><h2 class="text-xl font-bold text-white">Community chat</h2><p class="mt-1 text-sm text-slate-400">Messages are visible to everyone in the community.</p></div><span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-[10px] uppercase tracking-[0.15em] text-emerald-300">Live room</span></div>
                    <div class="mt-6 max-h-[34rem] space-y-3 overflow-y-auto pr-1">
                        @forelse ($messages as $message)
                            <article class="rounded-2xl border border-slate-800 bg-slate-950 p-4"><div class="flex items-center justify-between gap-3"><span class="font-semibold text-white">{{ $message->user->name }}</span><time class="text-xs text-slate-500" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time></div><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-300">{{ $message->body }}</p></article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-700 p-8 text-center text-sm text-slate-400">No messages yet. Start the conversation.</div>
                        @endforelse
                    </div>
                    <div class="mt-6 border-t border-slate-800 pt-5">
                        @auth
                            <form method="POST" action="{{ route('community.messages.store') }}" class="space-y-3">@csrf<label for="body" class="sr-only">Message</label><textarea id="body" name="body" rows="3" maxlength="1000" required placeholder="Say something to the community..." class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 focus:border-cyan-400 focus:ring-cyan-400">{{ old('body') }}</textarea><div class="flex items-center justify-between gap-3"><span class="text-xs text-slate-500">1,000 characters maximum</span><button class="rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-400">Post message</button></div></form>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-700 p-4 text-sm text-slate-400"><a href="{{ route('login') }}" class="font-semibold text-cyan-300 hover:text-cyan-400">Log in</a> to join the conversation.</div>
                        @endauth
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-3xl border border-slate-800 bg-slate-900 p-5"><div class="flex items-center justify-between"><h2 class="text-xl font-bold text-white">Community activity</h2><span class="text-xs uppercase tracking-[0.15em] text-slate-500">Verified</span></div><div class="mt-5 space-y-3">@forelse ($recentFixtures as $fixture)<div class="rounded-2xl border border-slate-800 bg-slate-950 p-3"><p class="text-xs uppercase tracking-[0.18em] text-cyan-300">{{ $fixture->tournament->name }}</p><p class="mt-2 text-sm font-semibold text-white">{{ $fixture->officialResult->winner->name }} defeated {{ $fixture->officialResult->loser->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $fixture->officialResult->score_for }} - {{ $fixture->officialResult->score_against }} · {{ $fixture->updated_at->diffForHumans() }}</p></div>@empty<div class="rounded-2xl border border-dashed border-slate-700 p-5 text-sm text-slate-400">Verified results will appear here after the first admin-approved fixture.</div>@endforelse</div></section>
                    <section class="rounded-3xl border border-slate-800 bg-slate-900 p-5"><h2 class="text-xl font-bold text-white">Tournament rooms</h2><div class="mt-4 space-y-3">@forelse ($tournaments->take(4) as $tournament)<a href="{{ route('tournaments.show', $tournament) }}" class="block rounded-2xl border border-slate-800 bg-slate-950 p-3 transition hover:border-cyan-500/50"><p class="font-semibold text-white">{{ $tournament->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $tournament->fixtures_count }} fixtures · {{ $tournament->organizer->name }}</p></a>@empty<p class="text-sm text-slate-400">Tournament rooms will appear here when events are published.</p>@endforelse</div></section>
                </aside>
            </div>
        </main>
    </body>
</html>