<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComicPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'comic_id',
        'page_number',
        'image_path',
        'alt_text'
    ];

    // Relación: Una página pertenece a un comic
    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    // Accessor: URL completa de la imagen de la página
    public function getImageUrlAttribute()
    {
        return 'C:\Users\Temporal\Desktop\comics\contents\pagescomics\\' . $this->image_path;
    }

    // Scope: Ordenar por número de página
    public function scopeOrdered($query)
    {
        return $query->orderBy('page_number');
    }

    // Método: Obtener página siguiente
    public function nextPage()
    {
        return static::where('comic_id', $this->comic_id)
                    ->where('page_number', '>', $this->page_number)
                    ->orderBy('page_number')
                    ->first();
    }

    // Método: Obtener página anterior
    public function previousPage()
    {
        return static::where('comic_id', $this->comic_id)
                    ->where('page_number', '<', $this->page_number)
                    ->orderBy('page_number', 'desc')
                    ->first();
    }
}