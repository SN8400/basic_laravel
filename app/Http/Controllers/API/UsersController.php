<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'List',
            'data'  => $users,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username'],
            'email'    => ['nullable','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
        ], [], [
            'name' => 'ชื่อ',
            'username' => 'ชื่อผู้ใช้',
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'username'  => $data['username'],
            'email'     => $data['email'] ?? null,
            'password'  => Hash::make($data['password']),
            'is_active' => 1,
        ]);

        return response()->json([
            'message' => 'Created',
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'is_active'=> (bool)$user->is_active,
            ],
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }
        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'is_active'=> (bool)$user->is_active
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'name'     => ['sometimes','required','string','max:255'],
            'username' => ['sometimes','required','string','max:255', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['nullable','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
            'is_active'=> ['sometimes','boolean'],
        ]);

        
        $user->fill([
            'name'      => $data['name']     ?? $user->name,
            'username'  => $data['username'] ?? $user->username,
            'email'     => array_key_exists('email', $data) ? $data['email'] : $user->email,
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        // เปลี่ยนรหัสผ่านเฉพาะเมื่อส่งมา
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Updated',
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'is_active'=> (bool)$user->is_active,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }
        $auth = $request->user();
        if ((int) $auth->id === (int) $user->id) {
            return response()->json(['message' => 'ห้ามลบบัญชีผู้ใช้ของตนเอง'], 403);
        }
        

        $user->delete();

        return response()->json(['message' => 'Deleted'], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
