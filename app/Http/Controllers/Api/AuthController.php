<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // =========================================
    // LOGIN
    // POST /api/auth/login
    // Body: { email, password }
    // =========================================

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Revoke semua token lama agar tidak duplikat sesi
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    // =========================================
    // REGISTER
    // POST /api/auth/register
    // Body: { name, email, password, password_confirmation }
    // Catatan: role default = student, Admin dibuat manual via panel
    // =========================================

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role student secara otomatis
        $user->assignRole('student');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }

    // =========================================
    // LOGOUT
    // POST /api/auth/logout
    // Header: Authorization: Bearer {token}
    // =========================================

    public function logout(Request $request): JsonResponse
    {
        // Hapus hanya token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    // =========================================
    // ME — get current user
    // GET /api/auth/me
    // Header: Authorization: Bearer {token}
    // =========================================

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('student.department', 'student.classroom');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $user->image_url,
                'roles' => $user->getRoleNames(),
                'student' => $user->student ? [
                    'nis' => $user->student->nis,
                    'status' => $user->student->status,
                    'department' => $user->student->department?->name,
                    'classroom' => $user->student->classroom?->name,
                ] : null,
            ],
        ]);
    }
}