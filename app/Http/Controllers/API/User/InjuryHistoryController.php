<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\InjuryHistory;
use Illuminate\Http\Request;
use Log;

class InjuryHistoryController extends Controller
{
    //

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {

            $histories = InjuryHistory::where("user_id", $request->user()->id)
                ->latest()
                ->paginate(15);

            return response()->json([
                'status' => true,
                'message' => ' Riwayat Luka Berhasil Diambil',
                'data' => $histories->items(),
                'pagination' => [
                    'total' => $histories->total(),
                    'current_page' => $histories->currentPage(),
                    'last_page' => $histories->lastPage(),
                    'per_page' => $histories->perPage(),
                    'next_page_url' => $histories->nextPageUrl(),
                    'prev_page_url' => $histories->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Index Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil riwayat luka',
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
            $validated = $request->validate([
                'label' => 'required|string',
                'image' => 'nullable|string',
                'detected_at' => 'required|date',
                'notes' => 'nullable|string',
                'location' => 'nullbale|string',
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);
            $validated['user_id'] = $request->user()->id;

            return response()->json([
                'status' => true,
                'message' => ' riwayat luka sukses dibuat',
                'data' => $validated,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Store Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal Menambahkan Data',
                'error' => $e->getMessage(),
            ], 404);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        //
        try {
            $histories = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat Luka Berhasil Diambil',
                'data' => $histories,
            ]);
        } catch (\Exception $e) {
            Log::error('Show Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => '',
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
            $histories = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);
            $validated = $request->validate([
                'label' => 'nullable|string',
                'image' => 'nullable|string',
                'detected_at' => 'nullable|date',
                'notes' => 'nullable|string',
                'location' => 'nullbale|string',
                'updated_by' => auth()->user()->id,
            ]);

            $histories->update($validated);
            return response()->json([
                'status' => 'success',
                'message' => ' Riwayat Luka Sukses Di Updated',
                'data' => $histories
            ]);
        } catch (\Exception $e) {
            //throw $th;

            Log::error('Update Error' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => ' Gagal Memperbarui Data',
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
            $histories = InjuryHistory::where('user_id', $id)->findOrFail($id);
            $histories->delete();
            return response()->json([
                'status' => 'success',
                'message' => ' Riwayat Luka Berhasil Dihapus',
                'data' => $histories
            ]);
        } catch (\Exception $e) {
            //throw $th;
            Log::error('Delete Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => ' Gagal Menghapus Riwayat Luka',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
}
