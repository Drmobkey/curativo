<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $users = User::with("roles")->get();
            return response()->json([
                "status" => true,
                "message" => "List Semua User",
                "data" => [
                    'Users' => $users,
                    'total' => $users->count()
                ],

            ],200);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                "status" => false,
                "message" => "Gagal Memuat Data",
                "error" => $e->getMessage(),
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request ->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'roles'=> 'array',
                'roles.*' => 'exists:roles,name',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }
            return response()->json([
                "status" => true,
                "message" => "User Berhasil Dibuat",
                "data" => [
                    'user' => $user->load('roles'),
                ],
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'status'=> false,
               'message'=> 'Gagal Membuat User',
               'error'=> $e->getMessage(),
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $user = User::with('roles')->findOrFail($id);
            return response()->json([
                "status" => true,
                "message" => "Detail User",
                "data" => [
                    'user' => $user,
                ],
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan",
                "error" => $e->getMessage(),
                ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'roles' => 'array',
                'roles.*' => 'exists:roles,name',
            ]);
            $updateData = $request->only(['name', 'email']);
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            return response()->json([
                "status" => true,
                "message" => "User Berhasil Diperbarui",
                "data" => [
                    'user' => $user->load('roles'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
               'status'=> false,
              'message'=> 'Gagal Memperbarui User',
               'error'=> $e->getMessage(),
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                "status" => true,
                "message" => "User Berhasil Dihapus",
            ],200);
        } catch (\Exception $e) {
            //throw $e;
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan",
                "error" => $e->getMessage(),
                ],404);
        }
    }
}
