<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListaDesejos;

class ListaDesejosController extends Controller
{
    /**
     * Adiciona um produto à lista de desejos do usuário logado.
     */
    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'id_produto' => 'required|integer|exists:produtos,id_produto',
        ]);

        $user = $request->user();
        $listaDesejos = ListaDesejos::firstOrCreate(['id_user' => $user->id]);

        // Adiciona o produto à lista, sem duplicar
        $listaDesejos->produtos()->syncWithoutDetaching([$validatedData['id_produto']]);
        
        $listaDesejosAtualizada = $listaDesejos->load('produtos');

        return response()->json([
            'message' => 'Produto adicionado à lista de desejos!',
            'lista_desejos' => $listaDesejosAtualizada
        ]);
    }

    /**
     * Exibe a lista de desejos do usuário logado.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $listaDesejos = ListaDesejos::where('id_user', $user->id)->with('produtos')->first();

        // Se o usuário não tem uma lista ou se ela está vazia, retorna uma resposta amigável.
        if (!$listaDesejos || $listaDesejos->produtos->isEmpty()) {
            return response()->json(['message' => 'Sua lista de desejos está vazia.'], 200);
        }

        return response()->json($listaDesejos);
    }

    /**
     * Remove um produto da lista de desejos.
     */
    public function remove(Request $request, $id_produto)
    {
        $user = $request->user();
        $listaDesejos = $user->listaDesejos; // Usando o relacionamento que definimos no Model User

        if (!$listaDesejos) {
            return response()->json(['message' => 'Lista de desejos não encontrada.'], 404);
        }

        // O método detach remove o relacionamento na tabela de ligação
        $listaDesejos->produtos()->detach($id_produto);

        // Retorna a lista atualizada
        return response()->json($listaDesejos->load('produtos'));
    }
}