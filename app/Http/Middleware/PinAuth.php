<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PinAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        $token  = str_starts_with($header, 'Bearer ') ? substr($header, 7) : '';

        $expected = hash_hmac('sha256', env('APP_PIN', '3738'), env('APP_KEY', ''));

        if (!$token || !hash_equals($expected, $token)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
