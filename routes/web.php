<?php

use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/ideas', [IdeaController::class, 'index'])->name('ideas.index');
Route::get('/ideas/create', [IdeaController::class, 'create'])->name('ideas.create');
Route::post('/ideas/create', [IdeaController::class, 'store'])->name('ideas.store');
Route::get('/ideas/edit/{idea}', [IdeaController::class, 'edit'])->name('ideas.edit');
Route::put('/ideas/update/{idea}', [IdeaController::class, 'update'])->name('ideas.update');
Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('ideas.show');
Route::delete('/ideas/{idea}', [IdeaController::class, 'delete'])->name('ideas.delete');
Route::put('/ideas/{idea}', [IdeaController::class, 'synchronizeLikes'])->name('ideas.like');