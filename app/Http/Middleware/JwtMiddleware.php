<?php
/**
 * Created by PhpStorm.
 * Cards: Cards
 * Date: 12/9/2018
 * Time: 1:02 AM
 */

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Cards;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = explode(' ',$request->header('Authorization'));

        $token = $token[1];

        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
        $user = Cards::find($credentials->sub);

        $request->auth = $user;

        return $next($request);
    }
}