<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Get(
 *     path="/api/posts",
 *     tags={"Posts"},
 *     summary="Получить список постов",
 *     @OA\Response(
 *         response=200,
 *         description="Список постов",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Post")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Посты не найдены"
 *     )
 * )
 */
public function index() {
    $posts = Post::paginate(10);
    return response()->json($posts);
}

/**
 * @OA\Get(
 *     path="/api/posts/{slug}",
 *     tags={"Posts"},
 *     summary="Получить пост по slug",
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         required=true,
 *         description="Slug поста",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пост найден",
 *         @OA\JsonContent(ref="#/components/schemas/Post")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пост не найден"
 *     )
 * )
 */
public function show($slug) {
    $post = Post::where('slug', $slug)->first();
    if (!$post) {
        return response()->json(['message' => 'Post not found'], 404);
    }
    return response()->json($post);
}

/**
 * @OA\Post(
 *     path="/api/posts",
 *     tags={"Posts"},
 *     summary="Создать новый пост",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","content"},
 *             @OA\Property(property="title", type="string", example="Название поста"),
 *             @OA\Property(property="content", type="string", example="Контент поста"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Пост создан",
 *         @OA\JsonContent(ref="#/components/schemas/Post")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверные данные"
 *     )
 * )
 */
public function store(Request $request) {
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $post = Post::create([
        'title' => $request->title,
        'slug' => Str::slug($request->title),
        'content' => $request->content,
    ]);

    return response()->json($post, 201);
}

/**
 * @OA\Put(
 *     path="/api/posts/{slug}",
 *     tags={"Posts"},
 *     summary="Обновить пост по slug",
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         required=true,
 *         description="Slug поста",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","content"},
 *             @OA\Property(property="title", type="string", example="Новое название поста"),
 *             @OA\Property(property="content", type="string", example="Обновленный контент поста"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пост обновлен",
 *         @OA\JsonContent(ref="#/components/schemas/Post")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пост не найден"
 *     )
 * )
 */
public function update(Request $request, $slug) {
    $post = Post::where('slug', $slug)->first();
    if (!$post) {
        return response()->json(['message' => 'Post not found'], 404);
    }

    $post->update($request->all());
    return response()->json($post);
}

/**
 * @OA\Delete(
 *     path="/api/posts/{slug}",
 *     tags={"Posts"},
 *     summary="Удалить пост по slug",
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         required=true,
 *         description="Slug поста",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Пост удален"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пост не найден"
 *     )
 * )
 */
public function destroy($slug) {
    $post = Post::where('slug', $slug)->first();
    if (!$post) {
        return response()->json(['message' => 'Post not found'], 404);
    }

    $post->delete();
    return response()->json(['message' => 'Post deleted']);
}
