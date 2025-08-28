<?php
// Arquivo: app/Models/Imagem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imagem extends Model
{
    use HasFactory;

    protected $table = 'imagens';
    protected $primaryKey = 'id_imagem';
    protected $fillable = ['id_produto', 'url_imagem'];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }
}