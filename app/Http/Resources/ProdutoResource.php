<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id_publico,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'preco' => $this->preco,
            'qtdEstoque' => $this->qtd_estoque,
            'vlrVenda' => $this->valor_venda,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'imagens' => ImagemResource::collection($this->whenLoaded('imagens')),
        ];
    }
}
