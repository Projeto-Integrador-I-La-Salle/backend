<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desconto;
use Illuminate\Http\Request;

class DescontoController extends Controller
{

    /**
     * Lista todos os descontos. (Admin)
     */
    public function index(Request $request)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json(Desconto::paginate(10));
    }

    /**
     * Cria um novo desconto. (Admin)
     */
    public function store(Request $request)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'porcentagem' => 'required|numeric|min:0|max:99.99',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $desconto = Desconto::create($validatedData);

        return response()->json($desconto, 201);
    }

    /**
     * Exibe um desconto específico. (Admin)
     */
    public function show(Request $request, Desconto $desconto)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json($desconto);
    }

    /**
     * Atualiza um desconto existente. (Admin)
     */
    public function update(Request $request, Desconto $desconto)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'porcentagem' => 'sometimes|required|numeric|min:0|max:99.99',
            'data_inicio' => 'sometimes|nullable|date',
            'data_fim' => 'sometimes|nullable|date|after_or_equal:data_inicio',
        ]);

        $desconto->update($validatedData);

        return response()->json($desconto);
    }

    /**
     * Remove um desconto. (Admin)
     */
    public function destroy(Request $request, Desconto $desconto)
    {
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $desconto->delete();

        return response()->json(null, 204);
    }
}