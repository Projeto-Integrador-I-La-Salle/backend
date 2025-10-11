<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CarrinhoController;
use App\Http\Controllers\Api\ListaDesejosController;
use App\Http\Controllers\Api\DescontoController;
use App\Http\Controllers\Api\CategoriaEspecialController;

// Rotas de Autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas de Produtos
Route::get('/produtos', [ProdutoController::class, 'index']);
Route::get('/produtos/{uuid}', [ProdutoController::class, 'show'])
    ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}');

// Rotas que EXIGEM AUTENTICAÇÃO
Route::middleware('auth:sanctum')->group(function () {
    // Rota para buscar o usuário logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- Produtos (Administração) ---
    Route::post('/produtos', [ProdutoController::class, 'store']);
    Route::put('/produtos/{uuid}', [ProdutoController::class, 'update']);
    Route::delete('/produtos/{uuid}', [ProdutoController::class, 'destroy']);
    Route::post('/produtos/importar-estoque', [ProdutoController::class, 'import']);
    Route::post('/produtos/{uuid}/imagens', [ProdutoController::class, 'addImage']);

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

});
