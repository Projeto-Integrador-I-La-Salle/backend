<?php
// Arquivo: app/Models/Reserva.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    protected $fillable = [
        'id_usuario',
        'valor_total',
        'status',
        'data_reserva'
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ReservaItem::class, 'id_reserva', 'id_reserva');
    }
}