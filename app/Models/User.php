<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // Importar HasOne

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id_publico',
        'name',
        'email',
        'password',
        'telefone',
        'permissao',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // RELACIONAMENTOS ENTRE AS TABELAS
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'id');
    }

    public function carrinho(): HasOne
    {
        return $this->hasOne(CarrinhoCompra::class, 'id_user', 'id');
    }

    public function listaDesejos(): HasOne
    {
        return $this->hasOne(ListaDesejos::class, 'id_user', 'id');
    }
}