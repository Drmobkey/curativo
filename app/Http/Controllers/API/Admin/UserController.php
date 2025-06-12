<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $currentUser = auth()->user();
            $currentRole = $currentUser->roles->first()->name ?? null;
            if ($currentRole === 'superadamin') {
                # code...
                $users = User::with("roles")->paginate(15);
            } else {
                # code...
                $users = User::with('roles')
                    ->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'admin');
                    })->paginate(15);
            }

            
    
            return response()->json([
                "status" => true,
                "message" => "List Semua User",
                "data" => [
                    'Users' => $users,
                    'total' => $users->count()
                ],
                'paginantion' =>[
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                ]

            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Gagal Memuat Data",
                "error" => $e->getMessage(),
            ], 500);
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
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'roles' => 'array',
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
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Membuat User',
                'error' => $e->getMessage(),
            ], 500);
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
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan",
                "error" => $e->getMessage(),
            ], 404);
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
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Memperbarui User',
                'error' => $e->getMessage(),
            ], 500);
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
            ], 200);
        } catch (\Exception $e) {
            //throw $e;
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan",
                "error" => $e->getMessage(),
            ], 404);
        }
    }
}
