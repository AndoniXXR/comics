<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComicRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comic_id',
        'rating'
    ];

    // Relación: Una calificación pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una calificación pertenece a un comic
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    // Scope: Calificaciones altas (8-10)
    public function scopeHigh($query)
    {
        return $query->where('rating', '>=', 8);
    }

    // Scope: Calificaciones medias (5-7)
    public function scopeMedium($query)
    {
        return $query->whereBetween('rating', [5, 7]);
    }

    // Scope: Calificaciones bajas (1-4)
    public function scopeLow($query)
    {
        return $query->where('rating', '<=', 4);
    }
}