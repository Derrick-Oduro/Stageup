<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TournamentController;
use App\Models\Tournament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredTournaments = Tournament::query()
        ->public()
        ->with('organizer:id,name')
        ->withCount(['followers as real_followers_count', 'participants as real_participants_count'])
        ->orderByDesc('starts_at')
        ->limit(3)
        ->get();

    return view('welcome', [
        'featuredTournaments' => $featuredTournaments,
    ]);
})->name('home');

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament:slug}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/fixtures', [CommunityController::class, 'fixtures'])->name('fixtures.index');
Route::get('/table', [CommunityController::class, 'table'])->name('table.index');
Route::get('/community', [CommunityController::class, 'community'])->name('community.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'can:access-admin-dashboard'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/tournaments', [AdminController::class, 'tournaments'])->name('admin.tournaments');
    Route::post('/admin/tournaments/{tournament:slug}/transition', [AdminController::class, 'transitionStatus'])->name('admin.tournaments.transition');
    Route::get('/admin/fixtures', [AdminController::class, 'fixtures'])->name('admin.fixtures');
    Route::post('/admin/fixtures/{fixture}/verify', [AdminController::class, 'verifyFixture'])->name('admin.fixtures.verify');
});

Route::middleware('auth')->group(function () {
    Route::post('/community/messages', [CommunityController::class, 'storeMessage'])->name('community.messages.store');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::post('/tournaments/{tournament:slug}/follow', [TournamentController::class, 'follow'])->name('tournaments.follow');
    Route::delete('/tournaments/{tournament:slug}/follow', [TournamentController::class, 'unfollow'])->name('tournaments.unfollow');
    Route::post('/tournaments/{tournament:slug}/join', [TournamentController::class, 'join'])->name('tournaments.join');
    Route::delete('/tournaments/{tournament:slug}/join', [TournamentController::class, 'leave'])->name('tournaments.leave');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
