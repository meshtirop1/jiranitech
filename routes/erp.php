<?php

use App\Http\Controllers\Erp;
use Illuminate\Support\Facades\Route;

/*
| The delivery system, served on its own hostname.
|
| Registered inside a domain group in bootstrap/app.php, so nothing here appears
| on the public site even though it shares the codebase. Route names are all
| prefixed erp. so the two never collide.
*/

Route::get('/login', [Erp\SessionController::class, 'create'])->name('login');
Route::post('/login', [Erp\SessionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login.store');
Route::post('/logout', [Erp\SessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth', 'delivery'])->group(function () {
    Route::get('/', Erp\DashboardController::class)->name('dashboard');

    Route::get('/projects', [Erp\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [Erp\ProjectController::class, 'show'])->name('projects.show');

    Route::get('/tasks/{task}', [Erp\TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [Erp\TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/transition', [Erp\TaskController::class, 'transition'])->name('tasks.transition');
    Route::post('/tasks/{task}/updates', [Erp\TaskController::class, 'comment'])->name('tasks.comment');

    Route::get('/reviews', [Erp\ReviewController::class, 'index'])->name('reviews.index');

    // Directing work: creating projects, tasks and enrolments.
    Route::middleware('delivery.director')->group(function () {
        Route::get('/projects-new', [Erp\ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects-new', [Erp\ProjectController::class, 'store'])->name('projects.store');
        Route::get('/people', [Erp\PeopleController::class, 'index'])->name('people.index');
        Route::post('/people', [Erp\PeopleController::class, 'store'])->name('people.store');
        Route::put('/people/{user}', [Erp\PeopleController::class, 'update'])->name('people.update');
    });

    // Creating and assigning tasks needs project-level authority, checked in the
    // controller against the project rather than globally.
    Route::post('/projects/{project}/tasks', [Erp\TaskController::class, 'store'])->name('tasks.store');
    Route::post('/projects/{project}/members', [Erp\ProjectController::class, 'addMember'])->name('projects.members.store');
});
