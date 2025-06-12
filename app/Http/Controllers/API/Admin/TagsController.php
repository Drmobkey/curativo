<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            //code...
            $tag = Tags::with(['creator', 'editor', 'tags'])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
            return response()->json([
                'status' => true,
                'message' => 'Sukses mendapatkan tag',
                'data' => $tag,
                'pagination' => [
                    'total' => $tag->total(),
                    'per_page' => $tag->perPage(),
                    'current_page' => $tag->currentPage(),
                    'last_page' => $tag->lastPage(),
                    'next_page_url' => $tag->nextPageUrl(),
                    'prev_page_url' => $tag->previousPageUrl(),
                ],
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan tag',
                'error' => $e->getMessage(),
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
                'name' => 'required|string|unique:tags,name',
            ]);
            $tag = Tags::create([
                'name' => $validate['name'],
                'slug' => Str::slug($validate['name']),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Sukses membuat tag',
                'data' => $tag,
            ], 201);

        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat tag',
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
            $tag = Tags::with(['creator', 'editor', 'tags'])->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Sukses mendapatkan tag',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan tag',
                'error' => $e->getMessage(),
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

            $tag = Tags::findOrFail($id);
            $validate = $request->validate([
                'name' => 'required|string|unique:tags,name,' . $id,
            ]);

            $tag->update([
                'name' => $validate['name'],
                'slug' => Str::slug($validate['name']),
                'updated_by' => Auth::id(),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Sukses mengupdate tag',
                'data' => $tag,
            ], 200);

        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate tag',
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
            $tag = Tags::findOrFail($id);
            $tag->tags()->detach();
            $tag->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sukses menghapus tag',
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
