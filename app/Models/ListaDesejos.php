<?php
// Arquivo: app/Models/ListaDesejos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListaDesejos extends Model
{
    use HasFactory;

    protected $table = 'lista_desejos';
    protected $primaryKey = 'id_lista_desejos';
    protected $fillable = ['id_user'];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'lista_desejos_produtos', 'id_lista_desejos', 'id_produto');
    }
}