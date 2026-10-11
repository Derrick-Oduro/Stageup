<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tournament management | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-400">Game Street</p>
                        <h1 class="text-2xl font-semibold">Tournament management</h1>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-400">
                            Dashboard
                        </a>
                        <a href="{{ route('home') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-400">
                            Back to site
                        </a>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-6 py-10">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach ($tournaments as $tournament)
                        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h2 class="text-xl font-semibold text-white">{{ $tournament->name }}</h2>
                                        <span class="rounded-full border border-cyan-500/40 bg-cyan-500/10 px-2 py-1 text-xs font-medium uppercase tracking-[0.2em] text-cyan-300">
                                            {{ str_replace('_', ' ', $tournament->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-400">{{ $tournament->game }} · Organized by {{ $tournament->organizer?->name ?? 'Unknown' }}</p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @php($availableTransitions = $tournament->status && isset(App\Models\Tournament::STATUS_TRANSITIONS[$tournament->status]) ? App\Models\Tournament::STATUS_TRANSITIONS[$tournament->status] : [])

                                    @forelse ($availableTransitions as $nextStatus)
                                        <form method="POST" action="{{ route('admin.tournaments.transition', ['tournament' => $tournament, 'status' => $nextStatus]) }}">
                                            @csrf
                                            <button type="submit" class="rounded-full bg-cyan-500 px-3 py-2 text-xs font-medium text-slate-950 transition hover:bg-cyan-400">
                                                Move to {{ str_replace('_', ' ', $nextStatus) }}
                                            </button>
                                        </form>
                                    @empty
                                        <span class="rounded-full border border-slate-700 bg-slate-950 px-3 py-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                                            No transitions available
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tournaments->links() }}
                </div>
            </main>
        </div>
    </body>
</html>
