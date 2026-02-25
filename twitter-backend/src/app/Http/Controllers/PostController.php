<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    // 投稿一覧取得
    public function index(Request $request)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // 投稿一覧を取得（ユーザー名といいね数も含む、created_at降順）
        $posts = Post::with(['user:id,name'])
            ->withCount('likes')
            ->withExists([
                'likes as is_liked' => function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'posts' => $posts,
            'message' => 'Successfully retrieved posts'
        ]);
    }

    // 投稿1件取得
    public function show(Request $request, Post $post)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // 対象の投稿を取得（ユーザー情報といいね数も含む）
        $targetPost = Post::with(['user:id,name', 'comments:content'])
            ->withCount('likes')
            ->withExists([
                'likes as is_liked' => function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }
            ])
            ->where('id', $post->id)
            ->first();

        return response()->json([
            'post' => $targetPost,
            'message' => 'Successfully retrieved post'
        ]);
    }

    // 投稿作成
    public function store(Request $request)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // 投稿を登録する
        $post = Post::create([
            'user_id' => $user_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Successfully post created',
        ], 201);
    }

    // 投稿削除
    public function destroy(Request $request, Post $post)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // 削除対象の投稿
        $deletePost = Post::where('id', $post->id)->first();

        // 投稿者か確認
        if ($deletePost->user->firebase_uid !== $firebaseUid) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        // 投稿を削除する
        $deletePost->delete();

        return response()->json([
            'message' => 'Successfully post deleted',
        ], 200);
    }
}
