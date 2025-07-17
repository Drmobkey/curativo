<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\InjuryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InjuryHistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            
            $history = InjuryHistory::where("user_id", $request->user()->id)
                ->latest()
                ->paginate(15);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat luka berhasil diambil',
                'data' => $history->items(),
                'pagination' => [
                    'total' => $history->total(),
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                    'per_page' => $history->perPage(),
                    'next_page_url' => $history->nextPageUrl(),
                    'prev_page_url' => $history->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Index Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil riwayat luka',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'label' => 'required|string',
                'image' => 'nullable|string',
                'detected_at' => 'required|date',
                'notes' => 'nullable|string',
                'location' => 'nullable|string',
                'scores' => 'nullable|numeric'
            ]);

            $validated['user_id'] = $request->user()->id;
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $history = InjuryHistory::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat luka berhasil ditambahkan',
                'data' => $history,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id, Request $request)
    {
        try {
            $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat luka berhasil diambil',
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            Log::error('Show Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);

            $validated = $request->validate([
                'label' => 'nullable|string',
                'image' => 'nullable|string',
                'detected_at' => 'nullable|date',
                'notes' => 'nullable|string',
                'location' => 'nullable|string',
                'scores' => 'nullable|numeric',
            ]);

            $validated['updated_by'] = auth()->id();
            $history->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat luka berhasil diperbarui',
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            Log::error('Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id, Request $request)
    {
        try {
            $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);
            $history->delete();

            return response()->json([
                'status' => true,
                'message' => 'Riwayat luka berhasil dihapus',
                'data' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus riwayat luka',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
