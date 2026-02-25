<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
        $firebaseUid = $request->attributes->get('firebase_uid');

        // Firebase AuthenticationのUIDからユーザIDを取得
        $user_id = User::where('firebase_uid', $firebaseUid)->first()->id;

        // いいねを取得する
        $like = Like::where('user_id', $user_id)
            ->where('post_id', $post->id)
            ->first();

        // いいねが存在する場合は削除、存在しない場合は追加
        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post->id,
            ]);
        }

        return response()->json([
            'message' => 'Successfully like toggled',
        ], 200);
    }
}
