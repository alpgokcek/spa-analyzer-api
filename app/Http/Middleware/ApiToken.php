<?php

namespace App\Http\Middleware;
use App\User;
use Closure;

class ApiToken
{
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');
        if ($auth) {
            $token = str_replace('Bearer ', '', $auth);
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Token'
                ], 401);
            }
            $user = User::where('api_token', $token)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not match'
                ], 401);
            }
            auth()->setUser($user);

            return $next($request);
        }
        return response()->json([
            'success' => false,
            'message' => 'No a valid token'
        ], 401);
    }
}
