<?php
// Arquivo: app/Models/Produto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Importar BelongsToMany

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    protected $fillable = [
        'id_publico',
        'id_categoria',
        'nome',
        'descricao',
        'preco',
        'qtd_estoque',
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function imagens(): HasMany
    {
        return $this->hasMany(Imagem::class, 'id_produto', 'id_produto');
    }

    public function descontos(): BelongsToMany
    {
        return $this->belongsToMany(Desconto::class, 'produtos_descontos', 'id_produto', 'id_desconto');
    }

    public function categoriasEspeciais(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaEspecial::class, 'categorias_especiais_produtos', 'id_produto', 'id_categorias_especiais');
    }
}