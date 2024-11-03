<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //$user->assignRole('user'); // Default role assignment
        $user->assignRole('user');
        return response()->json(['user' => $user, 'success' => true], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        $roleName = $user->getRoleNames()->first() ?? 'No role assigned';
        $data = [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $roleName,
        ];

        return response()->json(['data' => $data, 'token' => $token, 'success' => true]);
    }

    public function userList()
    {
        $users = User::all();

        return response()->json(['status' => true, 'message' => 'User Data Fetch Successfully', 'data' => UserResource::collection($users)], 200);
    }

    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = $request->role;
            $user->assignRole($role);
            return response()->json(['user' => $user, 'success' => true], 201);
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }
    }
}
