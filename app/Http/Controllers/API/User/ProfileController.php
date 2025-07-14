<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show(string $id)
    {
        //
        try {
            $currentUser = auth()->user();
            $user = User::findOrFail($id);

            if (!$currentUser) {
                return response()->json([
                    "status" => false,
                    "message" => 'User tidak ditemukan atau belum login'
                ], 401);
            }

            return response()->json([
                "status" => true,
                "message" => "Profil user berhasil dimuat",
                "data" => $user
            ], 200);

        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "terjadi kesalahan saat mengambil data user",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        //

        try {
            $currentUser = auth()->user();

            if (!$currentUser) {
                return response()->json([
                    "status" => false,
                    "message" => 'User tidak ditemukan atau belum login'
                ], 401);
            }

            $user = User::findOrFail($id);

            if ($currentUser->id !== $user->id) {
                return response()->json([
                    "status" => false,
                    "message" => "Anda tidak memiliki izin untuk mengakses data ini"
                ], 403);
            }


            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'no_telp' => 'nullable|string|max:20',
            ]);

            $updateData = $request->only(['name', 'email', 'jenis_kelamin', 'no_telp']);
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                "status" => true,
                "message" => "Profile berhasil diperbarui",
                "data" => $updateData,

            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan saat memperbarui profil",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

}
