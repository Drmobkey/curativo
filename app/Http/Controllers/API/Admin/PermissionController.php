<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json([
                'status' => true,
                'message' => 'Daftar Permission berhasil dimuat',
                'data' => [
                    'permissions' => $permissions,
                    'total' => $permissions->count()
                ],
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data permission',
                'error' => $e->getMessage()
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
            //code...
            $request->validate([
                'name' => 'required|string|unique:permissions,name',
            ]);

            $permission = Permission::create(['name' => $request->name]);
            return response()->json([
                'status' => true,
                'message' => 'Permission berhasil ditambahkan',
                'data' => ['permission' => $permission]
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Permission gagal ditambahkan',
                'error' => $e->getMessage()
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
            $permsission = Permission::find($id);
            return response()->json([
                'status' => true,
                'message' => 'Permission berhasil ditampilkan',
                'data' => ['permission' => $permsission]
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Permission gagal ditampilkan',
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
                'name' => 'required|string|unique:permissions,name,' . $id,
            ]);
            $permission = Permission::find($id);
            $permission->update(['name' => $request->name]);
            return response()->json([
                'status' => true,
                'message' => 'Permission berhasil diupdate',
                'data' => ['permission' => $permission]
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Permission gagal diupdate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $permission = Permission::find($id);
            $permission->delete();
            return response()->json([
                'status' => true,
                'message' => 'Permission berhasil dihapus',
                'data' => ['permission' => $permission]
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Permission gagal dihapus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
