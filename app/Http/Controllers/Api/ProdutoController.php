<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use App\Imports\ProdutosImport;
use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

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
        $produtos = Produto::with(['categoria', 'imagens', 'descontos', 'categoriasEspeciais'])->paginate($perPage); // Paginar com 10 itens por página

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
        $produto = Produto::where('id_publico', $uuid)->with(['categoria', 'imagens', 'descontos', 'categoriasEspeciais'])->first();

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

    public function import(Request $request)
    {
        try {
            $request->validate([
                'produtos' => [
                    'required',
                    File::types(['xlsx'])
                        ->min('1kb')
                        ->max('1mb')
                ]
            ]);

            Excel::import(
                new ProdutosImport,
                $request->file('produtos')
            );

            return response()->json(
                ['message' => 'Upload concluído com sucesso'],
                200
            );
        } catch (ValidationException $e) {
            $errors = $e->errors();

            return response()->json([
                'status' => 'error',
                'message' => 'Erro de validação',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $e) {
            Log::error("[ERROR]: Não foi possível importar .xlsx ", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocorreu um erro ao importar planilha de produtos.'
            ], 500);
        }
    }

    public function addImage(Request $request, string $uuid)
    {
        try {
            /*
            if ($request->user()->permissao !== 'admin') {
                return response()->json([
                    'message' => 'Acesso negado. Apenas administradores podem adicionar imagens.'
                ], 403);
            }
        */

            $request->validate([
                'image.*' => 'required|image|max:5120', // max 5MB
            ]);
            Log::info('[INFO]: Validação de input ocorreu com sucesso.');

            Log::info('[INFO]: Iniciando busca do produt.');
            $produto = Produto::where('id_publico', $uuid)->first();

            if (!$produto) {
                return response()->json([
                    'message' => 'Produto não encontrado'
                ], 404);
            }

            $urls = [];
            Log::info('[INFO]: Iniciando processamento das imagens.');
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('images', 'public');
                    Log::info('[INFO]: Path ->' . $path);

                    $urls[] = asset("storage/$path");
                    foreach ($urls as $url) {
                        Log::info('[INFO]: Url ->' . $url);
                        $produto->imagens()->create([
                            'url_imagem' => $url
                        ]);
                    }
                }
            }

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();

            return response()->json([
                'status' => 'error',
                'message' => 'Erro de validação',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $e) {
            Log::error("[ProdutoController.addImage] =>", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocorreu um erro ao salvar as imagens.'
            ], 500);
        }
    }

    /**
     * Associa um desconto a um produto. (Admin)
     */
    public function attachDesconto(Request $request, string $uuid)
    {
        // 1. Verificação de Permissão
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // 2. Validação: garante que o ID do desconto foi enviado e que ele existe
        $validatedData = $request->validate([
            'id_desconto' => 'required|integer|exists:descontos,id_descontos'
        ]);

        // 3. Encontra o produto pelo UUID
        $produto = Produto::where('id_publico', $uuid)->first();
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 4. Associa o desconto ao produto usando o relacionamento
        // O método syncWithoutDetaching é ideal: ele adiciona a associação sem criar duplicatas.
        $produto->descontos()->syncWithoutDetaching([$validatedData['id_desconto']]);

        // 5. Retorna o produto com a lista de descontos atualizada
        return response()->json($produto->load('descontos'));
    }

    /**
     * Desassocia um desconto de um produto. (Admin)
     */
    public function detachDesconto(Request $request, string $uuid, string $id_desconto)
    {
        // 1. Verificação de Permissão
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // 2. Encontra o produto pelo UUID
        $produto = Produto::where('id_publico', $uuid)->first();
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 3. Desassocia o desconto do produto
        // O método detach remove a linha correspondente na tabela de ligação.
        $produto->descontos()->detach($id_desconto);

        // 4. Retorna o produto com a lista de descontos atualizada
        return response()->json($produto->load('descontos'));
    }

    /**
     * Associa uma categoria especial a um produto. (Admin)
     */
    public function attachCategoriaEspecial(Request $request, string $uuid)
    {
        // 1. Verificação de Permissão
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // 2. Validação
        $validatedData = $request->validate([
            'id_categorias_especiais' => 'required|integer|exists:categorias_especiais,id_categorias_especiais'
        ]);

        // 3. Encontra o produto pelo UUID
        $produto = Produto::where('id_publico', $uuid)->first();
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 4. Associa a categoria especial ao produto
        $produto->categoriasEspeciais()->syncWithoutDetaching([$validatedData['id_categorias_especiais']]);

        // 5. Retorna o produto com a lista de categorias especiais atualizada
        return response()->json($produto->load('categoriasEspeciais'));
    }

    /**
     * Desassocia uma categoria especial de um produto. (Admin)
     */
    public function detachCategoriaEspecial(Request $request, string $uuid, string $id_categoria_especial)
    {
        // 1. Verificação de Permissão
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // 2. Encontra o produto pelo UUID
        $produto = Produto::where('id_publico', $uuid)->first();
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // 3. Desassocia a categoria especial do produto
        $produto->categoriasEspeciais()->detach($id_categoria_especial);

        // 4. Retorna o produto com a lista de categorias especiais atualizada
        return response()->json($produto->load('categoriasEspeciais'));
    }
}
