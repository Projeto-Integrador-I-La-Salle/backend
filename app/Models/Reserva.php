<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // Relacionamento com Usuário
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    // --- ADICIONE OU CONFIRA ESTE MÉTODO ---
    public function itens(): HasMany
    {
        // Uma reserva tem muitos itens (ReservaItem)
        return $this->hasMany(ReservaItem::class, 'id_reserva', 'id_reserva');
    }
}