<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InjuryHistory;
use Log;


class InjuryHistoryController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        //
        try {
            $query = InjuryHistory::with('user:id,name,email')
            ->orderBy('detected_at','desc');

            if ($request->has('user_id')) {
                $query->where('user_id',$request->user()->id);
            }
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('detected_at',[$request->start_date,$request->end_date]);
            }
            elseif ($request->has('start_date')) {
                $query->whereDate('detected_at','>=',$request->start_date );
            }
            elseif ($request->has('end_date')) {
                $query->whereDate('detected_at','<=',$request->end_date );
            }

            $histories = $query->paginate(15);

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
            //throw $th;
            Log::error('Index Error'.$e->getMessage());

            return response()->json([
                'status'=> false,
                'message'=> 'Gagal Mengambil Data Riwayat Luka',
                'error'=> $e->getMessage(),
            ],500);

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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        //
        try {
            $histories = InjuryHistory::with('user:id,name,email')->findOrFail($id); 

            return response()->json([
                'status'=> true,
                'message'=> ' Detail Riwayat Luka',
                'data'=> $histories
                ],200);
        } catch (\Exception $e) {
            //throw $th;
            Log::error('Show Error'.$e->getMessage());

            return response()->json([
                'status'=> false,
                'message'=> ' Data Tidak Ditemukan',
                'error'=> $e->getMessage(),
                ],500);
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
            $validated = $request->validate([
                'label' => 'nullable|string',
                'image' => 'nullable|string',
                'detected_at' => 'nullable|date',
                'notes' => 'nullable|string',
                'location' => 'nullbale|string',
                'updated_by' => auth()->user()->id,
            ]);

            $histories = InjuryHistory::findOrFail( $id );
            $histories->update($validated);

            return response()->json([
                'status'=> true,
                'message'=> '',
                'data'=> $histories
                ],200);

        } catch (\Exception $e) {
            Log::error('Update Error'.$e->getMessage());
            return response()->json([
                'status'=> false,
                'message'=> 'Gagal Memperbarui Data',
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
            $histories = InjuryHistory::findOrFail( $id );
            $histories->delete();

            return response()->json([
                'status'=> true,
                'message'=> '',
                'data'=> $histories
                ],200);

        } catch (\Exception $e) {
            Log::error('destroy error'.$e->getMessage());

            return response()->json([
                'status'=> false,
                'message'=> 'Gagal Menghapus Data',
                'error'=> $e->getMessage(),
                ],500);
        }
    }
}
