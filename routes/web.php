<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EntityController as AdminEntityController;
use App\Http\Controllers\Owner\EntityController as OwnerEntityController;
use App\Models\User;
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

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::view('/home', 'home')->name('home');
});

Route::middleware(['auth', 'role:'.User::ROLE_SUPER_ADMIN])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/entities', [AdminEntityController::class, 'index'])->name('admin.entities.index');
    Route::get('/entities/create', [AdminEntityController::class, 'create'])->name('admin.entities.create');
    Route::post('/entities', [AdminEntityController::class, 'store'])->name('admin.entities.store');
    Route::get('/entities/{entity}', [AdminEntityController::class, 'show'])->name('admin.entities.show');
    Route::get('/entities/{entity}/edit', [AdminEntityController::class, 'edit'])->name('admin.entities.edit');
    Route::match(['put', 'patch'], '/entities/{entity}', [AdminEntityController::class, 'update'])->name('admin.entities.update');
    Route::get('/places/search', [AdminEntityController::class, 'searchPlaces'])->name('admin.places.search');
});

Route::middleware(['auth', 'role:'.User::ROLE_SUPER_ADMIN.','.User::ROLE_EDITOR])->prefix('editor')->group(function () {
    Route::view('/', 'editor.dashboard')->name('editor.dashboard');
});

Route::middleware(['auth', 'role:'.User::ROLE_SUPER_ADMIN.','.User::ROLE_OWNER])->prefix('owner')->group(function () {
    Route::get('/', [OwnerEntityController::class, 'index'])->name('owner.dashboard');
    Route::get('/entities/create', [OwnerEntityController::class, 'create'])->name('owner.entities.create');
    Route::post('/entities', [OwnerEntityController::class, 'store'])->name('owner.entities.store');
    Route::get('/entities/{entity}/edit', [OwnerEntityController::class, 'edit'])->name('owner.entities.edit');
    Route::match(['put', 'patch'], '/entities/{entity}', [OwnerEntityController::class, 'update'])->name('owner.entities.update');
    Route::get('/entities/{entity}', [OwnerEntityController::class, 'show'])->name('owner.entities.show');
    Route::get('/places/search', [OwnerEntityController::class, 'searchPlaces'])->name('owner.places.search');
});
