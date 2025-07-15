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
                "data" => [
                    "name" => $user->name,
                    "email" => $user->email,
                    "jenis_kelamin" => $user->jenis_kelamin,
                    "no_telp" => $user->no_telp,
                    "tanggal_lahir" => $user->tanggal_lahir,
                ]
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

            // Validasi input
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'no_telp' => 'nullable|string|max:20',
                'tanggal_lahir' => 'nullable|date',
                'password' => 'nullable|string|min:6'
            ]);

            // Siapkan data untuk update
            $updateData = $request->only(['name', 'email', 'jenis_kelamin', 'no_telp', 'tanggal_lahir']);

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update user
            $user->update($updateData);

            return response()->json([
                "status" => true,
                "message" => "Profil berhasil diperbarui",
                "data" => $user->only(['id', 'name', 'email', 'jenis_kelamin', 'no_telp', 'tanggal_lahir']),
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan saat memperbarui profil",
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
