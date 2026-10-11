<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Game Street Admin</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-400">Game Street</p>
                        <h1 class="text-2xl font-semibold">Admin dashboard</h1>
                    </div>
                    <a href="{{ route('home') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-400">
                        Back to site
                    </a>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-6 py-10">
                <section class="mb-8 grid gap-4 md:grid-cols-4">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                        <p class="text-sm text-slate-400">Total users</p>
                        <p class="mt-3 text-3xl font-bold text-cyan-400">{{ $totalUsers }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                        <p class="text-sm text-slate-400">Tournaments</p>
                        <p class="mt-3 text-3xl font-bold text-violet-400">{{ $totalTournaments }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                        <p class="text-sm text-slate-400">Open registration</p>
                        <p class="mt-3 text-3xl font-bold text-emerald-400">{{ $openRegistrations }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                        <p class="text-sm text-slate-400">Live events</p>
                        <p class="mt-3 text-3xl font-bold text-amber-400">{{ $liveTournaments }}</p>
                    </div>
                </section>

                <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                        <h2 class="text-xl font-semibold">Governance checklist</h2>
                        <ul class="mt-5 space-y-3 text-sm text-slate-300">
                            <li class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3">
                                <span>Role matrix</span>
                                <span class="rounded-full bg-emerald-500/15 px-2 py-1 text-xs font-medium text-emerald-300">Ready</span>
                            </li>
                            <li class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3">
                                <span>Profile and audit foundation</span>
                                <span class="rounded-full bg-amber-500/15 px-2 py-1 text-xs font-medium text-amber-300">Next</span>
                            </li>
                            <li class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3">
                                <span>Official results workflow</span>
                                <span class="rounded-full bg-sky-500/15 px-2 py-1 text-xs font-medium text-sky-300">Planned</span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                        <h2 class="text-xl font-semibold">Quick actions</h2>
                        <div class="mt-5 space-y-3">
                            <a href="{{ route('tournaments.index') }}" class="block rounded-xl border border-slate-700 px-4 py-3 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-300">
                                Review tournaments
                            </a>
                            <a href="{{ route('dashboard') }}" class="block rounded-xl border border-slate-700 px-4 py-3 text-sm text-slate-200 transition hover:border-violet-400 hover:text-violet-300">
                                Member dashboard
                            </a>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
