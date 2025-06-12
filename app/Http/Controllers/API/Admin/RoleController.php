<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $roles = Role::with("permissions")->get();
            return response()->json([
                "status" => true,
                "message" => "List Semua Role",
                "data" => [
                    'Roles' => $roles,
                    'total' => $roles->count()
                ],
            ]);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Gagal Mengambil Data Role",
                "data" => ['Roles' => $roles],
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'required|array',
            ]);

            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'status' => true,
                'message' => 'Sukses Membuat Role',
                'data' => ['role' => $role->load('permissions')]
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk membuat role',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Sukses Mengambil Role',
                'data' => ['role' => $role]
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Mengambil Role',
                'error' => $e->getMessage()
            ], 500);
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
        //
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id,
                'permissions' => 'array',
            ]);

            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'status' => true,
                'message' => 'Sukses Mengupdate Role',
                'data' => ['role' => $role->load('permissions')]
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Mengupdate Role',
                'error' => $e->getMessage()
            ], 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {

            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sukses Menghapus Role',
                'data' => ['role' => $role]
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal Menghapus Role',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
