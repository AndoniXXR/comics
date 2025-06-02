<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación: Un usuario puede crear muchos comics
    public function comics()
    {
        return $this->hasMany(Comic::class);
    }

    // Relación: Un usuario puede tener muchas calificaciones
    public function ratings()
    {
        return $this->hasMany(ComicRating::class);
    }

    // Relación: Un usuario puede tener muchos favoritos
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    // Relación: Comics favoritos del usuario
    public function favoriteComics()
    {
        return $this->belongsToMany(Comic::class, 'user_favorites', 'user_id', 'comic_id')
                    ->withPivot('created_at')
                    ->orderBy('user_favorites.created_at', 'desc');
    }

    // Método: Verificar si un comic es favorito del usuario
    public function hasFavorite($comicId)
    {
        return $this->favorites()->where('comic_id', $comicId)->exists();
    }

    // Método: Agregar comic a favoritos
    public function addToFavorites($comicId)
    {
        if (!$this->hasFavorite($comicId)) {
            return $this->favorites()->create(['comic_id' => $comicId]);
        }
        return false;
    }

    // Método: Remover comic de favoritos
    public function removeFromFavorites($comicId)
    {
        return $this->favorites()->where('comic_id', $comicId)->delete();
    }

    // Método: Obtener calificación que el usuario dio a un comic
    public function getRatingForComic($comicId)
    {
        return $this->ratings()->where('comic_id', $comicId)->first();
    }

    // Accessor: URL completa de la foto de perfil
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return 'C:\Users\Temporal\Desktop\comics\contents\profilepics\\' . $this->photo;
        }
        return null;
    }

    // Método: Verificar si tiene foto de perfil
    public function hasProfilePhoto()
    {
        return !empty($this->photo) && file_exists($this->getPhotoUrlAttribute());
    }
}