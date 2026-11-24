<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /** Pendaftaran publik: selalu menjadi role customer (role tidak bisa dikirim dari request). */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => 'customer',
        ]);

        return $this->ok($this->tokenPayload($user), 'Registrasi berhasil.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user
            || ! Hash::check($data['password'], $user->password)
            || ! in_array($user->role, ['admin', 'operator', 'customer'], true)
        ) {
            return $this->fail('Email atau password salah.', 401);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return $this->ok($this->tokenPayload($user), 'Login berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Logout berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->item(UserResource::class, $request->user());
    }

    private function tokenPayload(User $user): array
    {
        return [
            'token'      => $user->createToken('cargotrace-api')->plainTextToken,
            'token_type' => 'Bearer',
            'user'       => (new UserResource($user))->resolve(),
        ];
    }
}
