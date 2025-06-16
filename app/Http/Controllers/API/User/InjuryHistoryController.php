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

            $history = InjuryHistory::where("user_id", $request->user()->id)
                ->latest()
                ->paginate(15);

            return response()->json([
                'status' => true,
                'message' => ' Riwayat Luka Berhasil Diambil',
                'data' => collect($history->items())->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'label' => $item->label,
                        'location' => $item->location,
                        'notes' => $item->notes,
                        'detected_at' => $item->detected_at,
                    ];
                }),
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
                'location' => 'nullable|string',
            ]);
            $validated['user_id'] = $request->user()->id;
            $validated['created_by'] = auth()->user()->id;
            $validated['updated_by'] = auth()->user()->id;

            $history = InjuryHistory::create($validated);

            return response()->json([
                'status' => true,
                'message' => ' riwayat luka sukses dibuat',
                'data' => [
                    'id' => $history->id,
                    'label' => $history->label,
                    'location' => $history->location,
                    'notes' => $history->notes,
                    'detected_at' => $history->detected_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Store Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal Menambahkan Data',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        //
        try {
            $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Riwayat Luka Berhasil Diambil',
                'data' =>
                    [
                        'id' => $history->id,
                        'label' => $history->label,
                        'location' => $history->location,
                        'notes' => $history->notes,
                        'detected_at' => $history->detected_at,
                    ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Show Error' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
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
            $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);
            $validated = $request->validate([
                'label' => 'nullable|string',
                'image' => 'nullable|string',
                'detected_at' => 'nullable|date',
                'notes' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            $validated['updated_by'] = auth()->user()->id;
            $history->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => ' Riwayat Luka Sukses Di Updated',
                'data' => [
                    'id'=> $history->id,
                    'label'=> $history->label,
                    'location'=> $history->location,
                    'notes'=> $history->notes,
                    'detected_at'=> $history->detected_at
                ]
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
    public function destroy(string $id, Request $request)
{
    try {
        $history = InjuryHistory::where('user_id', $request->user()->id)->findOrFail($id);
        $history->delete();

        return response()->json([
            'status' => true,
            'message' => 'Riwayat Luka Berhasil Dihapus',
            'data' => null
        ]);
    } catch (\Exception $e) {
        Log::error('Delete Error: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Gagal Menghapus Riwayat Luka',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
