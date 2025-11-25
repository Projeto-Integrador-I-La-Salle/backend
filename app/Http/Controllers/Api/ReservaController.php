<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reserva;
use App\Models\ReservaItem; // <<<--- Ajustado para o seu Model existente
use App\Models\CarrinhoCompra;

class ReservaController extends Controller
{
    /**
     * Lista as reservas do usuário logado.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Busca as reservas, carregando o relacionamento 'itens' e os produtos dentro deles
        $reservas = Reserva::where('id_usuario', $user->id)
            ->with(['itens.produto']) 
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reservas);
    }

/**
     * Cria uma nova reserva recebendo a lista de itens diretamente do Frontend.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // 1. Validação: O frontend DEVE enviar um array de itens
        $validatedData = $request->validate([
            'itens' => 'required|array|min:1',
            'itens.*.id_produto' => 'required|integer|exists:produtos,id_produto',
            'itens.*.quantidade' => 'required|integer|min:1',
            'telefone_contato' => 'required|string',
            'data_retirada' => 'required|date', 
            'metodo_pagamento' => 'required|string',
            'observacao' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($user, $validatedData) {
            
            $valorTotal = 0;
            $itensParaSalvar = [];

            // 2. Preparação e Validação de Estoque
            // Percorremos os itens enviados pelo frontend
            foreach ($validatedData['itens'] as $itemEnviado) {
                // Buscamos o produto no banco para pegar o PREÇO REAL e checar ESTOQUE REAL
                // (Nunca confie no preço enviado pelo frontend)
                $produto = \App\Models\Produto::find($itemEnviado['id_produto']);

                // Verifica estoque
                if ($produto->qtd_estoque < $itemEnviado['quantidade']) {
                    throw new \Exception("O produto '{$produto->nome}' não tem estoque suficiente (Solicitado: {$itemEnviado['quantidade']}, Disponível: {$produto->qtd_estoque}).");
                }

                // Calcula o preço
                $valorTotal += $produto->preco * $itemEnviado['quantidade'];

                // Guarda os dados para salvar depois
                $itensParaSalvar[] = [
                    'produto' => $produto,
                    'quantidade' => $itemEnviado['quantidade']
                ];
            }

            // 3. Criar a Reserva (Cabeçalho)
            $reserva = Reserva::create([
                'id_usuario' => $user->id,
                'valor_total' => $valorTotal,
                'status' => 'Pendente',
                'data_reserva' => now(),
                'telefone_contato' => $validatedData['telefone_contato'],
                'data_retirada' => $validatedData['data_retirada'],
                'metodo_pagamento' => $validatedData['metodo_pagamento'],
                'observacao' => $validatedData['observacao'] ?? null,
            ]);

            // 4. Salvar os Itens e Baixar Estoque
            foreach ($itensParaSalvar as $item) {
                $produto = $item['produto'];
                $qtd = $item['quantidade'];

                ReservaItem::create([
                    'id_reserva' => $reserva->id_reserva,
                    'id_produto' => $produto->id_produto,
                    'qtd_reservada' => $qtd,
                ]);

                // Baixa o estoque
                $produto->decrement('qtd_estoque', $qtd);
            }

            // Nota: Não precisamos mais limpar a tabela CarrinhoCompra, 
            // pois o frontend não está usando ela.

            return response()->json([
                'message' => 'Reserva realizada com sucesso!',
                'reserva' => $reserva->load('itens.produto')
            ], 201);
        });
    }

/**
     * ADMIN: Lista todas as reservas do sistema.
     */
    public function indexAdmin(Request $request)
    {
        // 1. Verificação de Segurança
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado. Apenas administradores.'], 403);
        }

        // 2. Busca todas as reservas
        // with('usuario'): Traz os dados de quem comprou
        // with('itens.produto'): Traz os produtos comprados
        $reservas = Reserva::with(['usuario', 'itens.produto'])
            ->orderBy('created_at', 'desc')
            ->paginate(20); // Paginação um pouco maior para admin

        return response()->json($reservas);
    }

    /**
     * ADMIN: Exibe detalhes de uma reserva específica.
     */
    public function showAdmin(Request $request, $id)
    {
        // 1. Verificação de Segurança
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        // 2. Busca a reserva pelo ID
        $reserva = Reserva::with(['usuario', 'itens.produto'])->find($id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva não encontrada.'], 404);
        }

        return response()->json($reserva);
    }

    /**
     * ADMIN: Atualiza o status de uma reserva.
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Verificação de Segurança (Apenas Admin)
        if ($request->user()->permissao !== 'admin') {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        // 2. Validação do Status
        // Definimos aqui quais são os "estados" permitidos para o fluxo da loja
        $validatedData = $request->validate([
            'status' => 'required|string|in:Pendente,Aprovado,Em Separação,Pronto para Retirada,Concluído,Cancelado'
        ]);

        // 3. Busca a reserva
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva não encontrada.'], 404);
        }

        // 4. Atualiza e Salva
        $reserva->status = $validatedData['status'];
        $reserva->save();

        return response()->json([
            'message' => 'Status da reserva atualizado com sucesso!',
            'reserva' => $reserva
        ]);
    }

    /**
     * Cancela uma reserva e devolve os itens ao stock.
     * Pode ser executado pelo Admin ou pelo Dono da reserva (se Pendente).
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        // 1. Busca a reserva com os itens para podermos devolver o stock
        $reserva = Reserva::with('itens')->find($id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva não encontrada.'], 404);
        }

        // 2. Verificação de Permissão e Regras de Negócio
        if ($user->permissao !== 'admin') {
            // Se não é admin, tem que ser o dono da reserva
            if ($reserva->id_usuario !== $user->id) {
                return response()->json(['message' => 'Não tem permissão para cancelar esta reserva.'], 403);
            }

            // Se for o dono, só pode cancelar se estiver 'Pendente'
            if ($reserva->status !== 'Pendente') {
                return response()->json(['message' => 'Não é possível cancelar uma reserva que já está em processamento. Entre em contacto com a loja.'], 400);
            }
        }

        // Se já estiver cancelada, não faz nada
        if ($reserva->status === 'Cancelado') {
            return response()->json(['message' => 'Esta reserva já foi cancelada.'], 400);
        }

        // 3. Executa o Cancelamento com Transação
        DB::transaction(function () use ($reserva) {
            
            // Devolve o stock de cada item
            foreach ($reserva->itens as $item) {
                // Incrementa a quantidade no produto
                // Nota: Usamos o Model Produto diretamente. Certifique-se de que o item tem o id_produto correto.
                // Uma forma segura é buscar o produto e incrementar.
                $produto = \App\Models\Produto::find($item->id_produto);
                if ($produto) {
                    $produto->increment('qtd_estoque', $item->qtd_reservada);
                }
            }

            // Atualiza o status para Cancelado
            $reserva->status = 'Cancelado';
            $reserva->save();
        });

        return response()->json([
            'message' => 'Reserva cancelada com sucesso e stock restaurado.',
            'reserva' => $reserva
        ]);
    }

}