<?php
// Arquivo: app/Models/ReservaItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaItem extends Model
{
    use HasFactory;

    protected $table = 'reserva_itens';
    protected $primaryKey = 'id_item_reserva';
    protected $fillable = [
        'id_reserva',
        'id_produto',
        'qtd_reservada',
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto', 'id_produto');
    }
}