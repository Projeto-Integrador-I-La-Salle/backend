<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Importar o Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Importar o Hash para senhas
use Illuminate\Support\Facades\Validator; // Importar o Validator para validação
use Illuminate\Support\Str; // Importar o Str para gerar o UUID
use Illuminate\Support\Facades\Auth; // Importar o Auth para o login

class AuthController extends Controller
{
    /**
     * CADASTRO
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'telefone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Criação do usuário
        $user = User::create([
            'id_publico' => Str::uuid(), // Gera o nosso id_publico
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Criptografa a senha
            'telefone' => $request->telefone,
            // 'permissao' já tem 'cliente' como padrão no banco de dados
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'user' => $user
        ], 201);
    }

    /**
     * LOGIN.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 1. Busca o usuário pelo e-mail
        $user = User::where('email', $request->email)->first();

        // 2. Verifica se o usuário existe E se a senha está correta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        // 3. Se tudo estiver certo, cria o token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}