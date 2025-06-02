<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name'
    ];

    // Relación: Un idioma puede tener muchos comics
    public function comics()
    {
        return $this->hasMany(Comic::class);
    }
}