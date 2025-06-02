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
        'is_active'
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relación: Un comic pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un comic pertenece a un idioma
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    // Relación: Un comic tiene muchas páginas
    public function pages()
    {
        return $this->hasMany(ComicPage::class)->orderBy('page_number');
    }

    // Relación: Un comic puede tener muchas calificaciones
    public function ratings()
    {
        return $this->hasMany(ComicRating::class);
    }

    // Relación: Un comic puede ser favorito de muchos usuarios
    public function favoritedBy()
    {
        return $this->hasMany(UserFavorite::class);
    }

    // Relación: Usuarios que marcaron este comic como favorito
    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'comic_id', 'user_id')
                    ->withPivot('created_at')
                    ->orderBy('user_favorites.created_at', 'desc');
    }

    // Scope: Solo comics activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Solo comics publicados
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Accessor: URL completa de la portada
    public function getCoverImageUrlAttribute()
    {
        return 'C:\Users\Temporal\Desktop\comics\contents\imagescomic\\' . $this->cover_image;
    }

    // Método: Verificar si un usuario ya calificó este comic
    public function isRatedByUser($userId)
    {
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    // Método: Obtener calificación de un usuario específico
    public function getUserRating($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }
}