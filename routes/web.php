<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SenderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\PrintHistoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/print-history', [PrintHistoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])->name('print-history');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/manage', function () {
        return Inertia::render('Manage');
    })->name('manage');

    Route::post('/users', [UserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 

    Route::get('/senders', [SenderController::class, 'index'])->name('senders.index');
    Route::post('/senders', [SenderController::class, 'store']);
    Route::put('/senders/{id}', [SenderController::class, 'update']);
    Route::delete('/senders/{id}', [SenderController::class, 'destroy']);
});

    Route::get('/contacts', [RecipientController::class, 'index'])->name('contacts.index');
    Route::post('/recipients', [RecipientController::class, 'store'])->name('recipients.store');
    Route::put('/recipients/{id}', [RecipientController::class, 'update'])->name('recipients.update');
    Route::post('/print-history', [PrintHistoryController::class, 'store'])
    ->middleware(['auth', 'verified']);
    Route::get('/print-history', [PrintHistoryController::class, 'index'])->name('print-history');
    Route::delete('/print-history/{id}', [PrintHistoryController::class, 'destroy'])
        ->middleware(['auth', 'verified', 'admin']);

require __DIR__.'/auth.php';
