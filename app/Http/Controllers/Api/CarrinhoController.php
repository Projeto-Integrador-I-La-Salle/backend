<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarrinhoCompra;
use App\Models\Produto;

class CarrinhoController extends Controller
{
    /**
     * Adiciona um produto ao carrinho do usuário logado.
     */
    public function add(Request $request)
    {
        // 1. Validação dos dados recebidos
        $validatedData = $request->validate([
            'id_produto' => 'required|integer|exists:produtos,id_produto',
            'quantidade' => 'required|integer|min:1'
        ]);

        // 2. Pega o usuário autenticado a partir do token
        $user = $request->user();

        // 3. Encontra o carrinho do usuário ou cria um novo se não existir
        $carrinho = CarrinhoCompra::firstOrCreate(['id_user' => $user->id]);

        // 4. Verifica se o produto já existe no carrinho
        $produtoNoCarrinho = $carrinho->produtos()->where('produtos.id_produto', $validatedData['id_produto'])->first();

        if ($produtoNoCarrinho) {
            // Se o produto já existe, atualiza a quantidade (soma a nova com a existente)
            $novaQuantidade = $produtoNoCarrinho->pivot->quantidade + $validatedData['quantidade'];
            $carrinho->produtos()->updateExistingPivot($validatedData['id_produto'], ['quantidade' => $novaQuantidade]);
        } else {
            // Se o produto não existe, adiciona-o ao carrinho
            $carrinho->produtos()->attach($validatedData['id_produto'], ['quantidade' => $validatedData['quantidade']]);
        }
        
        // 5. Retorna o carrinho atualizado com todos os seus produtos
        $carrinhoAtualizado = $carrinho->load('produtos');

        return response()->json([
            'message' => 'Produto adicionado ao carrinho com sucesso!',
            'carrinho' => $carrinhoAtualizado
        ]);
    }

    /**
     * Exibe o carrinho de compras do usuário logado.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Encontra o carrinho do usuário e carrega os produtos associados
        // O método with() é usado para evitar múltiplas queries (problema N+1)
        $carrinho = CarrinhoCompra::where('id_user', $user->id)
                                  ->with('produtos')
                                  ->first();

        if (!$carrinho) {
            return response()->json(['message' => 'Carrinho vazio.'], 200);
        }

        return response()->json($carrinho);
    }

    /**
     * Atualiza a quantidade de um produto no carrinho.
     */
    public function update(Request $request, $id_produto)
    {
        $validatedData = $request->validate([
            'quantidade' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $carrinho = $user->carrinho; // Usando o relacionamento que definimos no Model User

        if (!$carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado.'], 404);
        }

        // updateExistingPivot é o método Eloquent perfeito para atualizar uma tabela de ligação
        $carrinho->produtos()->updateExistingPivot($id_produto, [
            'quantidade' => $validatedData['quantidade']
        ]);

        return response()->json($carrinho->load('produtos'));
    }

    /**
     * Remove um produto do carrinho.
     */
    public function remove(Request $request, $id_produto)
    {
        $user = $request->user();
        $carrinho = $user->carrinho;

        if (!$carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado.'], 404);
        }

        // O método detach remove o relacionamento na tabela de ligação
        $carrinho->produtos()->detach($id_produto);

        return response()->json($carrinho->load('produtos'));
    }

}
