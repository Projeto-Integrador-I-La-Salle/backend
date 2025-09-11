<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImagemResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id_imagem,
            'url' => $this->url_imagem,
        ];
    }
}
