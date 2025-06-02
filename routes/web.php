<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página de inicio con comics
Route::get('/', function () {
    $comics = App\Models\Comic::with(['language', 'favoritedBy', 'pages'])
                                ->where('status', 'published')
                                ->where('is_active', true)
                                ->orderBy('rating', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->paginate(12);
    
    return view('welcome', compact('comics'));
})->name('home');

// Rutas de autenticación (accesibles solo para invitados)
Route::middleware('guest')->group(function () {
    // Mostrar formularios
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    
    // Procesar formularios
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Perfil de usuario
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Comics
    Route::resource('comics', ComicController::class);
    Route::post('/comics/{comic}/toggle-active', [ComicController::class, 'toggleActive'])->name('comics.toggle-active');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Dentro del middleware('auth')->group(function () {
    
    // Favoritos y calificaciones (agregar después de las rutas de comics)
    Route::post('/comics/{comic}/favorite', [ComicController::class, 'addToFavorites'])->name('comics.favorite');
    Route::post('/comics/{comic}/unfavorite', [ComicController::class, 'removeFromFavorites'])->name('comics.unfavorite');
    Route::post('/comics/{comic}/rate', [ComicController::class, 'rateComic'])->name('comics.rate');
    // Dentro del middleware('auth')->group(function () {
// Después de las rutas de comics existentes, agrega:

    Route::post('/comics/{comic}/favorite', [ComicController::class, 'addToFavorites'])->name('comics.favorite');
    Route::post('/comics/{comic}/unfavorite', [ComicController::class, 'removeFromFavorites'])->name('comics.unfavorite');
    Route::post('/comics/{comic}/rate', [ComicController::class, 'rateComic'])->name('comics.rate');
    // Dentro del middleware('auth')->group(function () {
    Route::post('/comics/{comic}/reorder-pages', [ComicController::class, 'reorderPages'])->name('comics.reorder-pages');
    // Dentro del middleware('auth')->group(function () {
    Route::post('/comics/{comic}/reorder-pages', [ComicController::class, 'reorderPages'])->name('comics.reorder-pages');
    Route::delete('/comics/pages/{page}', [ComicController::class, 'deletePage'])->name('comics.delete-page');
    Route::post('/comics/{comic}/reorder-pages', [ComicController::class, 'reorderPages'])->name('comics.reorder-pages');
    Route::delete('/comics/pages/{page}', [ComicController::class, 'deletePage'])->name('comics.delete-page');
});