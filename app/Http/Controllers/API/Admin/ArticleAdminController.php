<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
// use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Tags;
use Illuminate\Support\Facades\Auth;



class ArticleAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $articles = Article::with(['categories', 'tags', 'creator', 'editor'])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $articles,
                'pagination' => [
                    'total' => $articles->total(),
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'next_page_url' => $articles->nextPageUrl(),
                    'prev_page_url' => $articles->previousPageUrl(),
                ]
            ], 200);


        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk mendapatkan article',
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
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|string',
                'status' => 'required|in:draft,published',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
            ]);
            # code...
            $article = Article::create([
                'title' => $validate['title'],
                'slug' => Str::slug($validate['title']),
                'content' => $validate['content'],
                'image' => $validate['image'],
                'status' => $validate['status'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $article->categories()->sync($validate['categories']);
            $article->tags()->sync($validate['tags']);
            $article->load('categories', 'tags', 'creator', 'editor');

            return response()->json([
                'status' => true,
                'message' => 'Article berhasil dibuat',
                'data' => $article,
            ], 200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk membuat article',
                'error' => $e->getMessage(),
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
            $article = Article::with(['categories', 'tags', 'creator', 'editor'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Article berhasil didapatkan',
                'data' => $article,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk mendapatkan article',
                'error' => $e->getMessage(),
            ], 404);
            //throw $th;
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
            $article = Article::findOrFail($id);

            $validate = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|string',
                'status' => 'required|in:draft,published',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id'
            ]);

            $article->update([
                'title' => $validate['title'],
                'slug' => Str::slug($validate['title']),
                'content' => $validate['content'],
                'image' => $validate['image'],
                'status' => $validate['status'],
                'updated_by' => Auth::id()
            ]);

            // Sync categories and tags
            $article->categories()->sync($request->categories);
            $article->tags()->sync($request->tags);

            // Load relationships
            $article->load(['categories', 'tags', 'creator', 'editor']);

            return response()->json([
                'status' => true,
                'message' => 'Article berhasil diupdate',
                'data' => $article
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk mengupdate article',
                'error' => $e->getMessage()
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
            $article = Article::findOrFail($id);
            $article->categories()->detach();
            $article->tags()->detach();
            $article->delete();
            return response()->json([
                'status' => true,
                'message' => 'Article berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal untuk menghapus article',
                'error' => $e->getMessage(),
            ], 500);
            //throw $th;
        }
    }
}
