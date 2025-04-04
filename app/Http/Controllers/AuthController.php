<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function auth(Request $request)
{
    $action = $request->input('action');

    if ($action === 'register') {
        // Validação
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        // Criar usuário
        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        // Gerar token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso',
            'token' => $token,
            'user' => $user
        ]);
    } 
    
    elseif ($action === 'login') {
        // Verificar credenciais
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'token' => $token,
            'user' => $user
        ]);
    }

    return response()->json(['message' => 'Ação inválida'], 400);
}
}