<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $input)
    {
        $validator = Validator::make($input->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email:rfc,dns', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Registrasi Gagal!',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = User::create([
                'name' => $input->name,
                'email' => $input->email,
                'password' => Hash::make($input['password']),
                'role_id' => $input->role_id,
            ]);
            $token = $data->createToken('myToken')->plainTextToken;
            $response = [
                'message' => "Registrasi Berhasil!",
                'data' => [
                    'user' => $data,
                    'token' => $token
                ]
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Registrasi Error!",
                'error' => $e->errorInfo
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function login(Request $input)
    {
        $validator = Validator::make($input->all(), [
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Registrasi Gagal!',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = User::where("email", $input->email)->first();
            if (!$user || !Hash::check($input->password, $user->password)) {
                return response()->json([
                    'message' => 'Login Gagal!',
                    "data" => !$user ? "Akun tidak ditemukan" : "Password Salah",
                ], Response::HTTP_UNAUTHORIZED);
            }
            $token = $user->createToken('myToken')->plainTextToken;
            $response = [
                'message' => "Login Berhasil!",
                'data' => [
                    'token' => $token
                ]
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Login Error!",
                'error' => $e->errorInfo
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function logout(Request $input)
    {
        $input->user()->currentAccessToken()->delete();
        $response = [
            'message' => "Logout Berhasil!",
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
