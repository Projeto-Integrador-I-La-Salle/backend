<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\CarrinhoController; 

// Rotas de Autenticação
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas de Produtos
Route::get('/produtos', [ProdutoController::class, 'index']);
Route::get('/produtos/{uuid}', [ProdutoController::class, 'show']);

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

    // --- Carrinho de Compras ---
    Route::get('/carrinho', [CarrinhoController::class, 'show']);
    Route::post('/carrinho/produtos', [CarrinhoController::class, 'add']);
    Route::put('/carrinho/produtos/{id_produto}', [CarrinhoController::class, 'update']);
    Route::delete('/carrinho/produtos/{id_produto}', [CarrinhoController::class, 'remove']);
    
});