<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fixtures review | Game Street</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-slate-100 antialiased">
        <div class="min-h-screen">
            <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-400">Game Street</p>
                        <h1 class="text-2xl font-semibold">Fixtures awaiting verification</h1>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-400">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.tournaments') }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-cyan-400 hover:text-cyan-400">
                            Tournament management
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

                <div class="space-y-5">
                    @forelse ($fixtures as $fixture)
                        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-cyan-400">{{ $fixture->tournament->name }}</p>
                                    <h2 class="mt-2 text-xl font-semibold">{{ $fixture->round_name ?? 'Fixture' }}</h2>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ $fixture->participants->map(fn($participant) => $participant->name)->join(' vs ') }}
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('admin.fixtures.verify', ['fixture' => $fixture]) }}" class="w-full max-w-xl space-y-3">
                                    @csrf

                                    <div class="grid gap-3 md:grid-cols-2">
                                        <label class="text-sm text-slate-300">
                                            Winner
                                            <select name="winner_user_id" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                                                @foreach ($fixture->participants as $participant)
                                                    <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <label class="text-sm text-slate-300">
                                            Loser
                                            <select name="loser_user_id" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                                                @foreach ($fixture->participants as $participant)
                                                    <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <label class="text-sm text-slate-300">
                                            Score for
                                            <input type="number" min="0" name="score_for" value="2" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                                        </label>

                                        <label class="text-sm text-slate-300">
                                            Score against
                                            <input type="number" min="0" name="score_against" value="1" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                                        </label>
                                    </div>

                                    <label class="block text-sm text-slate-300">
                                        Reason
                                        <textarea name="reason" rows="2" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">Scores match and were verified.</textarea>
                                    </label>

                                    <button type="submit" class="rounded-full bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950 transition hover:bg-cyan-400">
                                        Approve result
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 text-center text-slate-300">
                            No fixtures are awaiting verification.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $fixtures->links() }}
                </div>
            </main>
        </div>
    </body>
</html>
