<?php
// Arquivo: app/Models/Categoria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $fillable = ['tipo'];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class, 'id_categoria', 'id_categoria');
    }
}