<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Регистрация пользователя",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Пользователь зарегистрирован",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверные данные",
 *     )
 * )
 */
public function register(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json(['user' => $user], 201);
}

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Авторизация пользователя",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный логин",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="your-jwt-token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неверные учетные данные",
 *     )
 * )
 */
public function login(Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('YourAppName')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
}
