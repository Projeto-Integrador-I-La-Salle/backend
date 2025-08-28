<?php
// Arquivo: app/Models/CategoriaEspecial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaEspecial extends Model
{
    use HasFactory;

    protected $table = 'categorias_especiais';
    protected $primaryKey = 'id_categorias_especiais';
    protected $fillable = [
        'nome',
        'data_inicio',
        'data_fim',
        'porcentagem'
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'categorias_especiais_produtos', 'id_categorias_especiais', 'id_produto');
    }
}