<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página de inicio con comics (PÚBLICA) - Con estadísticas mejoradas
Route::get('/', function () {
    $comics = App\Models\Comic::with(['language', 'favoritedBy', 'pages'])
                                ->where('status', 'published')
                                ->where('is_active', true)
                                ->orderBy('rating', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->paginate(12);

    // Estadísticas para la página principal
    $totalComics = App\Models\Comic::where('status', 'published')
                                   ->where('is_active', true)
                                   ->count();
    
    $totalAuthors = App\Models\Comic::where('status', 'published')
                                    ->where('is_active', true)
                                    ->distinct('user_id')
                                    ->count();
    
    $totalPages = App\Models\Comic::where('status', 'published')
                                  ->where('is_active', true)
                                  ->withCount('pages')
                                  ->get()
                                  ->sum('pages_count');
    
    $totalLanguages = App\Models\Language::whereHas('comics', function($query) {
                                            $query->where('status', 'published')
                                                  ->where('is_active', true);
                                        })->count();
    
    return view('welcome', compact('comics', 'totalComics', 'totalAuthors', 'totalPages', 'totalLanguages'));
})->name('home');

// Ruta para servir imágenes de portadas de comics
Route::get('/comic-covers/{filename}', function ($filename) {
    $path = 'C:\Users\Temporal\Desktop\comics\contents\imagescomic\\' . $filename;
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('comic.cover');

// Mostrar comic individual (PÚBLICO - cualquiera puede leer comics publicados)
Route::get('/comics/{comic}', [ComicController::class, 'show'])->name('comics.show');

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
    
    // Comics - Gestión (crear, editar, eliminar requieren autenticación)
    Route::get('/comics', [ComicController::class, 'index'])->name('comics.index');
    Route::get('/comics/create', [ComicController::class, 'create'])->name('comics.create');
    Route::post('/comics', [ComicController::class, 'store'])->name('comics.store');
    Route::get('/comics/{comic}/edit', [ComicController::class, 'edit'])->name('comics.edit');
    Route::put('/comics/{comic}', [ComicController::class, 'update'])->name('comics.update');
    Route::delete('/comics/{comic}', [ComicController::class, 'destroy'])->name('comics.destroy');
    
    // Comics - Acciones adicionales
    Route::post('/comics/{comic}/toggle-active', [ComicController::class, 'toggleActive'])->name('comics.toggle-active');
    Route::post('/comics/{comic}/reorder-pages', [ComicController::class, 'reorderPages'])->name('comics.reorder-pages');
    Route::delete('/comics/pages/{page}', [ComicController::class, 'deletePage'])->name('comics.delete-page');
    
    // Favoritos y calificaciones
    Route::post('/comics/{comic}/favorite', [ComicController::class, 'addToFavorites'])->name('comics.favorite');
    Route::post('/comics/{comic}/unfavorite', [ComicController::class, 'removeFromFavorites'])->name('comics.unfavorite');
    Route::post('/comics/{comic}/rate', [ComicController::class, 'rateComic'])->name('comics.rate');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Ruta para servir páginas de comics
Route::get('/comic-pages/{filename}', function ($filename) {
    $path = 'C:\Users\Temporal\Desktop\comics\contents\pagescomics\\' . $filename;
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('comic.page');