<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editora extends Model
{
    protected $fillable = [
        'nome',
        'logotipo',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'nome' => 'encrypted',
            'logotipo' => 'encrypted',
            'notas' => 'encrypted',
        ];
    }

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }
}
