<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Usamos o Model Produto para buscar todos os produtos do banco.
        // O método 'with' é usado para carregar os relacionamentos (neste caso, a categoria e as imagens de cada produto).
        // Isso evita o problema de "N+1 queries" e torna a API mais eficiente.
        $perPage = $request->input('per_page', 10);
        $produtos = Produto::with(['categoria', 'imagens'])->paginate($perPage); // Paginar com 10 itens por página

        return ProdutoResource::collection($produtos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Verificação de Permissão: O usuário logado é admin?
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado. Apenas administradores podem criar produtos.'], 403); // 403 Forbidden
        }

        // 2. Validação dos Dados
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'qtd_estoque' => 'required|integer|min:0',
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
        ]);

        // 3. Criação do Produto
        $produto = Produto::create([
            'id_publico' => \Illuminate\Support\Str::uuid(), // Gera um novo UUID
            'nome' => $validatedData['nome'],
            'descricao' => $validatedData['descricao'],
            'preco' => $validatedData['preco'],
            'qtd_estoque' => $validatedData['qtd_estoque'],
            'id_categoria' => $validatedData['id_categoria'],
        ]);

        // 4. Retorno da Resposta
        // Usamos o 'load' para já trazer a categoria junto, assim a resposta é mais completa
        return response()->json($produto->load('categoria'), 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        // Usamos o método where() para buscar pelo nosso id_publico (UUID)
        // e o first() para pegar o primeiro (e único) resultado.
        // O with() carrega os relacionamentos, assim como no método index().
        $produto = Produto::where('id_publico', $uuid)->with(['categoria', 'imagens'])->first();

        // Se nenhum produto for encontrado com esse UUID, retornamos um erro 404
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // Se encontrarmos, retornamos o produto como JSON
        return new ProdutoResource($produto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        // 1. Verificação de Permissão: O usuário logado é admin?
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado. Apenas administradores podem editar produtos.'], 403);
        }

        // 2. Validação dos Dados
        // Usamos 'sometimes' para validar um campo apenas se ele estiver presente na requisição.
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|nullable|string',
            'preco' => 'sometimes|required|numeric|min:0',
            'qtd_estoque' => 'sometimes|required|integer|min:0',
            'id_categoria' => 'sometimes|required|integer|exists:categorias,id_categoria',
        ]);

        // 3. Encontrar o Produto
        $produto = Produto::where('id_publico', $uuid)->first();

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 4. Atualização do Produto
        // O método 'update' preenche o modelo apenas com os dados validados que foram enviados.
        $produto->update($validatedData);

        // 5. Retorno da Resposta
        // Retornamos o produto atualizado, já com a categoria carregada.
        return response()->json($produto->load('categoria'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $uuid)
    {
        // 1. Verificação de Permissão: O usuário logado é admin?
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado. Apenas administradores podem deletar produtos.'], 403);
        }

        // 2. Encontrar o Produto
        $produto = Produto::where('id_publico', $uuid)->first();

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 3. Deleção do Produto
        $produto->delete();

        // 4. Retorno da Resposta
        // Uma resposta de sucesso para delete não precisa de corpo, apenas o status 204.
        return response()->json(null, 204); // 204 No Content
    }
}
