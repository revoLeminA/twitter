<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentController extends Controller
{
    // コメント一覧取得
    public function index(Request $request, Post $post)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // コメント一覧を取得（ユーザー名も含む、created_at降順）
        $comments = Comment::where('post_id', $post->id)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments,
            'message' => 'Successfully retrieved comments'
        ]);
    }

    // コメント作成
    public function store(Request $request, Post $post)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // コメントを登録する
        $comment = Comment::create([
            'user_id' => $user_id,
            'post_id' => $post->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Successfully comment created',
        ], 201);
    }
}
