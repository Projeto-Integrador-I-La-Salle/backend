<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CarrinhoController;
use App\Http\Controllers\Api\ListaDesejosController;
use App\Http\Controllers\Api\DescontoController;
use App\Http\Controllers\Api\CategoriaEspecialController;
use App\Http\Controllers\Api\ReservaController;

// Rotas de Autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas de Produtos
Route::get('/produtos', [ProdutoController::class, 'index']);
Route::get('/produtos/{uuid}', [ProdutoController::class, 'show'])
    ->whereUuid('uuid');

// Rotas que EXIGEM AUTENTICAÇÃO
Route::middleware('auth:sanctum')->group(function () {
    // Rota para buscar o usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- Produtos (Administração) ---
    Route::post('/produtos', [ProdutoController::class, 'store']);
    Route::put('/produtos/{uuid}', [ProdutoController::class, 'update'])
        ->whereUuid('uuid');
    Route::delete('/produtos/{uuid}', [ProdutoController::class, 'destroy'])
        ->whereUuid('uuid');
    Route::post('/produtos/importar-estoque', [ProdutoController::class, 'import']);
    Route::post('/produtos/{uuid}/imagens', [ProdutoController::class, 'addImage'])
        ->whereUuid('uuid');

    // --- Carrinho de Compras ---
    Route::get('/carrinho', [CarrinhoController::class, 'show']);
    Route::post('/carrinho/produtos', [CarrinhoController::class, 'add']);
    Route::put('/carrinho/produtos/{id_produto}', [CarrinhoController::class, 'update']);
    Route::delete('/carrinho/produtos/{id_produto}', [CarrinhoController::class, 'remove']);

    // --- Lista de Desejos ---
    Route::get('/lista-desejos', [ListaDesejosController::class, 'show']);
    Route::post('/lista-desejos/produtos', [ListaDesejosController::class, 'add']);
    Route::delete('/lista-desejos/produtos/{id_produto}', [ListaDesejosController::class, 'remove']);

    // --- DESCONTOS (Admin) ---
    Route::apiResource('descontos', DescontoController::class);

    // --- CATEGORIAS ESPECIAIS (Admin) ---
    Route::apiResource('categorias-especiais', CategoriaEspecialController::class);

    // --- ASSOCIAÇÃO DE PRODUTOS (Admin) ---
    // Rotas para associar Descontos a Produtos
    Route::post('/produtos/{uuid}/descontos', [ProdutoController::class, 'attachDesconto']);
    Route::delete('/produtos/{uuid}/descontos/{id_desconto}', [ProdutoController::class, 'detachDesconto']);
    // Rotas para associar Categorias Especiais a Produtos
    Route::post('/produtos/{uuid}/categorias-especiais', [ProdutoController::class, 'attachCategoriaEspecial']);
    Route::delete('/produtos/{uuid}/categorias-especiais/{id_categoria_especial}', [ProdutoController::class, 'detachCategoriaEspecial']);

    // --- RESERVAS (Usuário) ---
    Route::get('/reservas', [ReservaController::class, 'index']); // Listar minhas reservas
    Route::post('/reservas', [ReservaController::class, 'store']); // Finalizar pedido (Checkout)
    Route::delete('/reservas/{id}', [ReservaController::class, 'cancel']); // Cancelar Reserva

    // --- RESERVAS (Administração) ---
    Route::get('/admin/reservas', [ReservaController::class, 'indexAdmin']);
    Route::get('/admin/reservas/{id}', [ReservaController::class, 'showAdmin']);
    Route::patch('/admin/reservas/{id}/status', [ReservaController::class, 'updateStatus']);

});
