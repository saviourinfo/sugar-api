<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $pin = $request->input('pin');

        if ($pin !== env('APP_PIN', '3738')) {
            return response()->json(['success' => false, 'message' => 'Wrong PIN'], 401);
        }

        // Deterministic token: hmac of pin + app key.
        // The middleware (see routes/api.php) validates this same value.
        $token = hash_hmac('sha256', $pin, env('APP_KEY', ''));

        return response()->json(['success' => true, 'token' => $token]);
    }
}
