<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaEspecial;
use Illuminate\Http\Request;

class CategoriaEspecialController extends Controller
{

    /**
     * Lista todas as categorias especiais. (Admin)
     */
    public function index(Request $request)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        return response()->json(CategoriaEspecial::paginate(10));
    }

    /**
     * Cria uma nova categoria especial. (Admin)
     */
    public function store(Request $request)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255|unique:categorias_especiais',
            'porcentagem' => 'required|numeric|min:0|max:99.99',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
        ]);
        $categoriaEspecial = CategoriaEspecial::create($validatedData);
        return response()->json($categoriaEspecial, 201);
    }

    /**
     * Exibe uma categoria especial específica. (Admin)
     * --- MÉTODO CORRIGIDO ---
     */
    public function show(Request $request, string $id)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $categoriaEspecial = CategoriaEspecial::find($id);
        if (!$categoriaEspecial) {
            return response()->json(['message' => 'Categoria Especial não encontrada'], 404);
        }

        return response()->json($categoriaEspecial);
    }

    /**
     * Atualiza uma categoria especial existente. (Admin)
     * --- MÉTODO CORRIGIDO ---
     */
    public function update(Request $request, string $id)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255|unique:categorias_especiais,nome,' . $id . ',id_categorias_especiais',
            'porcentagem' => 'sometimes|required|numeric|min:0|max:99.99',
            'data_inicio' => 'sometimes|required|date',
            'data_fim' => 'sometimes|required|date|after_or_equal:data_inicio',
        ]);

        $categoriaEspecial = CategoriaEspecial::find($id);
        if (!$categoriaEspecial) {
            return response()->json(['message' => 'Categoria Especial não encontrada'], 404);
        }

        $categoriaEspecial->update($validatedData);

        return response()->json($categoriaEspecial);
    }

    /**
     * Remove uma categoria especial. (Admin)
     * --- MÉTODO CORRIGIDO ---
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $categoriaEspecial = CategoriaEspecial::find($id);
        if (!$categoriaEspecial) {
            return response()->json(['message' => 'Categoria Especial não encontrada'], 404);
        }

        $categoriaEspecial->delete();

        return response()->json(null, 204);
    }
}