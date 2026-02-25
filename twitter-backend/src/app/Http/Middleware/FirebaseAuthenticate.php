<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidToken;

class FirebaseAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // クライアントから送られてきたIDトークンを取得
        $idToken = $request->bearerToken();

        // IDトークンが存在しない場合は、認証エラーを返す
        if (!$idToken) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        try {
            $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
            $auth = $factory->createAuth();

            // IDトークンを検証して、Firebase UIDを取得
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Firebase UIDをリクエスト属性にセットして、コントローラーで利用できるようにする
            $request->attributes->set('firebase_uid', $firebaseUid);
        } catch (InvalidToken $e) {
            return response()->json([
                'message' => 'Invalid token',
            ], 401);
        }

        return $next($request);
    }
}
