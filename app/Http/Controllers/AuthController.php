<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Регистрация
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:11',
            'password' => 'required|string|min:4|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    // Логин
// AuthController.php
    public function login(Request $request)
    {
        // Валидируем запрос
        $credentials = $request->only('email', 'password');

        // Пытаемся найти пользователя
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Создаем токен для пользователя
            $token = $user->createToken('API Token')->plainTextToken;

            // Возвращаем токен
            return response()->json(['token' => $token,
                'name' => $user->name,
                'surname' => $user->surname,
                'phone' => $user->phone,
                'email' => $user->email,
                'role' => $user->role,
            ], 200);
        }

        // Если не нашли пользователя или пароль неверный
        return response()->json(['message' => 'Unauthorized'], 401);
    }

}
