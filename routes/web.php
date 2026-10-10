<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('tournaments.index');
});

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament:slug}', [TournamentController::class, 'show'])->name('tournaments.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/tournaments/{tournament:slug}/follow', [TournamentController::class, 'follow'])->name('tournaments.follow');
    Route::delete('/tournaments/{tournament:slug}/follow', [TournamentController::class, 'unfollow'])->name('tournaments.unfollow');
    Route::post('/tournaments/{tournament:slug}/join', [TournamentController::class, 'join'])->name('tournaments.join');
    Route::delete('/tournaments/{tournament:slug}/join', [TournamentController::class, 'leave'])->name('tournaments.leave');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
