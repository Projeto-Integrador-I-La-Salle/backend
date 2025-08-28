<?php
// Arquivo: app/Models/Desconto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Desconto extends Model
{
    use HasFactory;

    protected $table = 'descontos';
    protected $primaryKey = 'id_descontos';
    protected $fillable = [
        'nome',
        'data_inicio',
        'data_fim',
        'porcentagem'
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'produtos_descontos', 'id_desconto', 'id_produto');
    }
}