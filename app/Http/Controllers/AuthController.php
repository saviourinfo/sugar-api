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
        $token = hash('sha256', $pin . env('APP_KEY'));
        return response()->json(['success' => true, 'token' => $token]);
    }
}