<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
     try {
        $categories = Category::with(['creator', 'updater', 'article'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        return response()->json([
            'status' => true,
            'message' => 'Category sukses ditampilkan',
            'data' => $categories,
            'pagination' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'next_page_url' => $categories->nextPageUrl(),
                'prev_page_url' => $categories->previousPageUrl(),
            ],
        ], 201);
     } catch (\Exception $e) {
        //throw $th;
        Log::error($e->getMessage());
        return response()->json([
           'status' => false,
           'message' => 'Terjadi kesalahan saat memuat category',
            'data' => $categories,
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
            $validate = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
            ]);

            $category = Category::create([
                'name' => $validate['name'],
                'slug' => Str::slug($validate['name']),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category sukses dibuat',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat category',
                'data' => $category,
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
            $category = Category::with(['creator', 'updater', 'article'])->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Category sukses ditampilkan',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat category',
                'data' => $category,
                'error' => $e->getMessage()
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
        //
        try {
            $category = Category::findOrFail($id);

            $validate = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
            ]);
            $category->update([
                'name' => $validate['name'],
                'slug' => Str::slug($validate['name']),
                'updated_by' => Auth::id()
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Category sukses diupdate',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat category',
                'data' => $category,
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
            $category = Category::findOrFail($id);
            $category->article()->detach();
            $category->delete();
            return response()->json([
                'status' => true,
                'message' => 'Category berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus Category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
