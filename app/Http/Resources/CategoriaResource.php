<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id_categoria,
            'tipo' => $this->tipo,
        ];
    }
}
