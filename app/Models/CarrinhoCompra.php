<?php
// Arquivo: app/Models/CarrinhoCompra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarrinhoCompra extends Model
{
    use HasFactory;

    protected $table = 'carrinho_compras';
    protected $primaryKey = 'id_carrinho_compras';
    protected $fillable = ['id_user'];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function produtos(): BelongsToMany
    {
        // Note o ->withPivot() para acessarmos a coluna 'quantidade'
        return $this->belongsToMany(Produto::class, 'carrinho_compras_produtos', 'id_carrinho_compras', 'id_produto')
                    ->withPivot('quantidade');
    }
}