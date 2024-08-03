<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "nomor_induk" => "required",
                "password" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::where("nomor_induk", $request->nomor_induk)->first();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => "Nomor induk tidak ditemukan"
                ], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => "Password anda salah"
                ], 401);
            }

            if (!$token = JWTAuth::attempt($request->only('nomor_induk', 'password'))) {
                return response()->json([
                    'message' => "Autentikasi gagal"
                ], 401);
            }

            $user = JWTAuth::user();

            return response()->json([
                'status' => 200,
                'user'    => $user,
                'token'   => $token
            ], 200);
        } catch (\Throwable $t) {
            Log::error('Error during login: ' . $t->getMessage());
            return response()->json([
                "error" => "Terjadi kesalahan pada server"
            ], 500);
        }
    }
}
