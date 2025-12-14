<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

        // Librarian only (CRUD)
    Route::middleware(['can:manage-books'])->group(function () {
        Route::get('/library/create', [BookController::class, 'create'])->name('library.create');
        Route::post('/library', [BookController::class, 'store'])->name('library.store');
        Route::get('/library/{book}/edit', [BookController::class, 'edit'])->name('library.edit');
        Route::put('/library/{book}', [BookController::class, 'update'])->name('library.update');
        Route::delete('/library/{book}', [BookController::class, 'destroy'])->name('library.destroy');
    });

    // Library (all users)
    Route::get('/library', [BookController::class, 'index'])->name('library.index');
    Route::get('/library/{book}', [BookController::class, 'show'])->name('library.show');



});


require __DIR__.'/settings.php';
