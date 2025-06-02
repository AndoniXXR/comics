<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'synopsis',
        'cover_image',
        'language_id',
        'status',
        'is_active',
        'rating'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:2'
    ];

    /**
     * Relación con el usuario (creador del comic)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el idioma
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Relación con las páginas del comic
     */
    public function pages()
    {
        return $this->hasMany(ComicPage::class)->orderBy('page_number');
    }

    /**
     * Relación con las calificaciones
     */
    public function ratings()
    {
        return $this->hasMany(ComicRating::class);
    }

    /**
     * Relación con los usuarios que marcaron como favorito
     */
    public function favoritedBy()
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Obtener la URL de la portada del comic
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            return route('comic.cover', ['filename' => $this->cover_image]);
        }
        return null;
    }

    /**
     * Obtener la ruta completa de la imagen de portada
     */
    public function getCoverPathAttribute()
    {
        if ($this->cover_image) {
            return 'C:\Users\Temporal\Desktop\comics\contents\imagescomic\\' . $this->cover_image;
        }
        return null;
    }

    /**
     * Verificar si la portada existe físicamente
     */
    public function hasCoverImage()
    {
        return $this->cover_image && file_exists($this->cover_path);
    }

    /**
     * Obtener el número total de páginas
     */
    public function getTotalPagesAttribute()
    {
        return $this->pages()->count();
    }

    /**
     * Obtener el promedio de calificaciones
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Obtener el total de favoritos
     */
    public function getTotalFavoritesAttribute()
    {
        return $this->favoritedBy()->count();
    }

    /**
     * Scopes para consultas comunes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    /**
     * Actualizar el rating promedio del comic
     */
    public function updateAverageRating()
    {
        $averageRating = $this->ratings()->avg('rating') ?? 0;
        $this->update(['rating' => round($averageRating, 2)]);
        return $this->rating;
    }

    /**
     * Verificar si un usuario específico puede editar este comic
     */
    public function canBeEditedBy($user)
    {
        return $user && $this->user_id === $user->id;
    }

    /**
     * Obtener comics relacionados (mismo idioma o autor)
     */
    public function getRelatedComics($limit = 4)
    {
        return self::where('id', '!=', $this->id)
                   ->where(function($query) {
                       $query->where('language_id', $this->language_id)
                             ->orWhere('author', $this->author);
                   })
                   ->published()
                   ->active()
                   ->orderBy('rating', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Formatear la sinopsis para mostrar
     */
    public function getFormattedSynopsisAttribute()
    {
        return nl2br(e($this->synopsis));
    }

    /**
     * Obtener el estado formateado
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'published' 
            ? '<span class="badge bg-success">Publicado</span>'
            : '<span class="badge bg-warning">Borrador</span>';
    }

    /**
     * Obtener el estado activo formateado
     */
    public function getActiveBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-primary">Activo</span>'
            : '<span class="badge bg-secondary">Inactivo</span>';
    }
}