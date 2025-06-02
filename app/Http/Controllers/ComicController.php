<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Language;
use App\Models\ComicPage;
use App\Models\ComicRating;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ComicController extends Controller
{
    /**
     * Mostrar lista de comics del usuario
     */
    public function index()
    {
        $userComics = Auth::user()->comics()
                          ->with(['language', 'pages'])
                          ->withCount(['ratings', 'favoritedBy'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);
        
        return view('comics.index', compact('userComics'));
    }

    /**
     * Mostrar formulario para crear nuevo comic
     */
    public function create()
    {
        $languages = Language::orderBy('name')->get();
        return view('comics.create', compact('languages'));
    }

    /**
     * Almacenar nuevo comic
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'synopsis' => 'required|string|max:1000',
            'language_id' => 'required|exists:languages,id',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB máximo
            'status' => 'required|in:draft,published',
            'pages.*' => 'required|image|mimes:jpeg,png,jpg|max:10240', // 10MB por página
        ], [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'author.required' => 'El autor es obligatorio',
            'author.max' => 'El nombre del autor no puede superar los 255 caracteres',
            'synopsis.required' => 'La sinopsis es obligatoria',
            'synopsis.max' => 'La sinopsis no puede superar los 1000 caracteres',
            'language_id.required' => 'Debes seleccionar un idioma',
            'language_id.exists' => 'El idioma seleccionado no es válido',
            'cover_image.required' => 'La portada es obligatoria',
            'cover_image.image' => 'La portada debe ser una imagen',
            'cover_image.mimes' => 'La portada debe ser JPG, JPEG o PNG',
            'cover_image.max' => 'La portada no debe superar los 5MB',
            'status.required' => 'Debes seleccionar un estado',
            'status.in' => 'El estado debe ser borrador o publicado',
            'pages.*.required' => 'Debes subir al menos una página',
            'pages.*.image' => 'Todas las páginas deben ser imágenes',
            'pages.*.mimes' => 'Las páginas deben ser JPG, JPEG o PNG',
            'pages.*.max' => 'Cada página no debe superar los 10MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Validar que se hayan subido páginas
        if (!$request->hasFile('pages') || count($request->file('pages')) === 0) {
            return redirect()->back()
                           ->withErrors(['pages' => 'Debes subir al menos una página del comic'])
                           ->withInput();
        }

        try {
            // Crear directorios si no existen
            $coverPath = 'C:\Users\Temporal\Desktop\comics\contents\imagescomic';
            $pagesPath = 'C:\Users\Temporal\Desktop\comics\contents\pagescomics';
            
            if (!file_exists($coverPath)) {
                mkdir($coverPath, 0755, true);
            }
            if (!file_exists($pagesPath)) {
                mkdir($pagesPath, 0755, true);
            }

            // Subir imagen de portada
            $coverImage = $request->file('cover_image');
            $coverName = 'cover_' . time() . '_' . Str::slug($request->title) . '.' . $coverImage->getClientOriginalExtension();
            $coverImage->move($coverPath, $coverName);

            // Crear el comic
            $comic = Comic::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'author' => $request->author,
                'synopsis' => $request->synopsis,
                'cover_image' => $coverName,
                'language_id' => $request->language_id,
                'status' => $request->status,
                'is_active' => true,
                'rating' => 0.00
            ]);

            // Subir páginas del comic
            $pages = $request->file('pages');
            foreach ($pages as $index => $page) {
                $pageName = 'page_' . $comic->id . '_' . ($index + 1) . '_' . time() . '.' . $page->getClientOriginalExtension();
                $page->move($pagesPath, $pageName);

                ComicPage::create([
                    'comic_id' => $comic->id,
                    'page_number' => $index + 1,
                    'image_path' => $pageName,
                    'alt_text' => 'Página ' . ($index + 1) . ' de ' . $comic->title
                ]);
            }

            return redirect()->route('comics.show', $comic->id)
                           ->with('success', '¡Comic creado exitosamente! 🎉');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'Error al crear el comic. Intenta de nuevo.'])
                           ->withInput();
        }
    }

    /**
     * Mostrar un comic específico
     */
    public function show($id)
    {
        $comic = Comic::with(['user', 'language', 'pages' => function($query) {
                            $query->orderBy('page_number');
                        }, 'ratings.user'])
                        ->withCount(['ratings', 'favoritedBy'])
                        ->findOrFail($id);

        // Verificar si el usuario actual ha marcado como favorito
        $isFavorite = Auth::check() ? Auth::user()->hasFavorite($comic->id) : false;
        
        // Verificar si el usuario actual ya calificó
        $userRating = Auth::check() ? Auth::user()->getRatingForComic($comic->id) : null;

        // Pre-cargar las imágenes para evitar problemas de carga
        foreach ($comic->pages as $page) {
            $imagePath = $page->image_url;
            if (file_exists($imagePath)) {
                // Verificar que el archivo existe y es legible
                $page->image_exists = true;
                $page->image_size = filesize($imagePath);
            } else {
                $page->image_exists = false;
                \Log::warning("Imagen no encontrada: " . $imagePath);
            }
        }

        return view('comics.show', compact('comic', 'isFavorite', 'userRating'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $comic = Comic::where('user_id', Auth::id())
                     ->with(['pages' => function($query) {
                         $query->orderBy('page_number');
                     }])
                     ->findOrFail($id);
        $languages = Language::orderBy('name')->get();
        
        return view('comics.edit', compact('comic', 'languages'));
    }

    /**
     * Actualizar comic
     */
    public function update(Request $request, $id)
    {
        $comic = Comic::where('user_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'synopsis' => 'required|string|max:1000',
            'language_id' => 'required|exists:languages,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'status' => 'required|in:draft,published',
            'new_pages.*' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Actualizar portada si se subió una nueva
            if ($request->hasFile('cover_image')) {
                $coverPath = 'C:\Users\Temporal\Desktop\comics\contents\imagescomic';
                
                // Eliminar portada anterior
                $oldCoverPath = $coverPath . '\\' . $comic->cover_image;
                if (file_exists($oldCoverPath)) {
                    unlink($oldCoverPath);
                }

                // Subir nueva portada
                $coverImage = $request->file('cover_image');
                $coverName = 'cover_' . time() . '_' . Str::slug($request->title) . '.' . $coverImage->getClientOriginalExtension();
                $coverImage->move($coverPath, $coverName);
                
                $comic->cover_image = $coverName;
            }

            // Procesar nuevas páginas si se subieron
            if ($request->hasFile('new_pages')) {
                $pagesPath = 'C:\Users\Temporal\Desktop\comics\contents\pagescomics';
                $currentMaxPage = $comic->pages()->max('page_number') ?? 0;
                
                foreach ($request->file('new_pages') as $index => $page) {
                    $pageNumber = $currentMaxPage + $index + 1;
                    $pageName = 'page_' . $comic->id . '_' . $pageNumber . '_' . time() . '.' . $page->getClientOriginalExtension();
                    $page->move($pagesPath, $pageName);

                    ComicPage::create([
                        'comic_id' => $comic->id,
                        'page_number' => $pageNumber,
                        'image_path' => $pageName,
                        'alt_text' => 'Página ' . $pageNumber . ' de ' . $comic->title
                    ]);
                }
            }

            // Actualizar datos del comic
            $comic->update([
                'title' => $request->title,
                'author' => $request->author,
                'synopsis' => $request->synopsis,
                'language_id' => $request->language_id,
                'status' => $request->status,
                'cover_image' => $comic->cover_image
            ]);

            return redirect()->route('comics.show', $comic->id)
                           ->with('success', 'Comic actualizado exitosamente! 🎉');

        } catch (\Exception $e) {
            \Log::error('Error actualizando comic: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'Error al actualizar el comic. Intenta de nuevo.'])
                           ->withInput();
        }
    }

    /**
     * Reordenar páginas del comic
     */
    public function reorderPages(Request $request, $id)
    {
        $comic = Comic::where('user_id', Auth::id())->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'pages' => 'required|array',
            'pages.*.id' => 'required|integer|exists:comic_pages,id',
            'pages.*.newNumber' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Verificar que todas las páginas pertenecen al comic del usuario
            $pageIds = collect($request->pages)->pluck('id');
            $validPages = ComicPage::where('comic_id', $comic->id)
                                  ->whereIn('id', $pageIds)
                                  ->count();
            
            if ($validPages !== count($pageIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunas páginas no pertenecen a este comic'
                ], 403);
            }

            // Actualizar el número de página para cada página
            foreach ($request->pages as $pageData) {
                ComicPage::where('id', $pageData['id'])
                        ->where('comic_id', $comic->id)
                        ->update(['page_number' => $pageData['newNumber']]);
            }

            \Log::info("Páginas reordenadas para comic {$comic->id} por usuario " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Orden de páginas actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error reordenando páginas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Eliminar una página específica
     */
    public function deletePage($pageId)
    {
        try {
            $page = ComicPage::findOrFail($pageId);
            $comic = Comic::where('user_id', Auth::id())->findOrFail($page->comic_id);
            
            // Eliminar archivo físico
            $pagePath = 'C:\Users\Temporal\Desktop\comics\contents\pagescomics\\' . $page->image_path;
            if (file_exists($pagePath)) {
                unlink($pagePath);
            }

            // Eliminar registro de la base de datos
            $pageNumber = $page->page_number;
            $page->delete();

            // Reordenar páginas restantes
            ComicPage::where('comic_id', $comic->id)
                    ->where('page_number', '>', $pageNumber)
                    ->decrement('page_number');

            return response()->json([
                'success' => true,
                'message' => 'Página eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error eliminando página: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la página'
            ], 500);
        }
    }

    /**
     * Eliminar comic
     */
    public function destroy($id)
    {
        $comic = Comic::where('user_id', Auth::id())->findOrFail($id);

        try {
            // Eliminar archivos físicos
            $coverPath = 'C:\Users\Temporal\Desktop\comics\contents\imagescomic\\' . $comic->cover_image;
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }

            // Eliminar páginas
            foreach ($comic->pages as $page) {
                $pagePath = 'C:\Users\Temporal\Desktop\comics\contents\pagescomics\\' . $page->image_path;
                if (file_exists($pagePath)) {
                    unlink($pagePath);
                }
            }

            // Eliminar registros de la base de datos
            $comic->pages()->delete();
            $comic->ratings()->delete();
            $comic->favoritedBy()->delete();
            $comic->delete();

            return redirect()->route('comics.index')
                           ->with('success', 'Comic eliminado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'Error al eliminar el comic.']);
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleActive($id)
    {
        $comic = Comic::where('user_id', Auth::id())->findOrFail($id);
        
        $comic->is_active = !$comic->is_active;
        $comic->save();

        $status = $comic->is_active ? 'activado' : 'desactivado';
        
        return redirect()->back()
                       ->with('success', "Comic {$status} correctamente.");
    }

    /**
     * Agregar comic a favoritos
     */
    public function addToFavorites($id)
    {
        $comic = Comic::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasFavorite($comic->id)) {
            UserFavorite::create([
                'user_id' => $user->id,
                'comic_id' => $comic->id,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comic agregado a favoritos'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'El comic ya está en favoritos'
        ]);
    }

    /**
     * Quitar comic de favoritos
     */
    public function removeFromFavorites($id)
    {
        $comic = Comic::findOrFail($id);
        $user = Auth::user();

        $removed = UserFavorite::where('user_id', $user->id)
                              ->where('comic_id', $comic->id)
                              ->delete();

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Comic quitado de favoritos'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al quitar de favoritos'
        ]);
    }

    /**
     * Calificar comic
     */
    public function rateComic(Request $request, $id)
    {
        $comic = Comic::findOrFail($id);
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Calificación inválida'
            ], 400);
        }

        // Actualizar o crear calificación
        ComicRating::updateOrCreate(
            [
                'user_id' => $user->id,
                'comic_id' => $comic->id
            ],
            [
                'rating' => $request->rating
            ]
        );

        // Recalcular rating promedio del comic
        $averageRating = $comic->ratings()->avg('rating');
        $comic->update(['rating' => round($averageRating, 2)]);

        return response()->json([
            'success' => true,
            'message' => 'Comic calificado exitosamente',
            'new_rating' => round($averageRating, 2)
        ]);
    }
}