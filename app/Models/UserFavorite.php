<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comic_id'
    ];

    // Solo tiene created_at, no updated_at
    const UPDATED_AT = null;
    
    protected $dates = ['created_at'];

    // Relación: Un favorito pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un favorito pertenece a un comic
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    // Scope: Favoritos de un usuario específico
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope: Favoritos de un comic específico
    public function scopeForComic($query, $comicId)
    {
        return $query->where('comic_id', $comicId);
    }
}