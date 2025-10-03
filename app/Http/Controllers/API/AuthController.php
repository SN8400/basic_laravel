<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !$user->is_active || !\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $token = $user->createToken($request->input('device_name','api-token'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        // ถ้าเรียกผ่าน Session (web guard)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out',
            'redirect_to' => route('login'),
        ]);
    }
}
