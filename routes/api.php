<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\ProdutoController;

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

    // Rota para criar um novo produto (será protegida para admin dentro do controller)
    Route::post('/produtos', [ProdutoController::class, 'store']);

    // Rota para atualizar um produto existente
    Route::put('/produtos/{uuid}', [ProdutoController::class, 'update']);

    // Rota para deletar um produto
    Route::delete('/produtos/{uuid}', [ProdutoController::class, 'destroy']);
    
});