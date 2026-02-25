<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // ユーザ登録
    public function store (Request $request)
    {
        try {
            // Middlewareで取得済みのFirebase AuthenticationのUIDを取得する
            $firebaseUid = $request->attributes->get('firebase_uid');

            // Firebase UIDを使用して、ユーザをデータベースに保存
            User::firstOrCreate(
                ['firebase_uid' => $firebaseUid],
                ['name' => $request->name],
            );

            return response()->json([
                'message' => 'Successfully user created',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
