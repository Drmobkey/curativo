<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

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

            // Ambil filter dari query parameter
            $filterRole = request()->query('role');

            // Validasi: hanya superadmin yang bisa pakai filter
            if ($currentRole === 'superadmin') {

                // Validasi isi filter role
                $allowedRoles = ['superadmin', 'admin', 'user'];
                if ($filterRole && !in_array($filterRole, $allowedRoles)) {
                    return response()->json([
                        "status" => false,
                        "message" => "Role filter tidak valid. Gunakan: superadmin, admin, atau user.",
                    ], 400);
                }

                // Jika valid, lanjutkan query
                $users = User::with('roles')
                    ->when($filterRole, function ($query) use ($filterRole) {
                        $query->whereHas('roles', function ($q) use ($filterRole) {
                            $q->where('name', $filterRole);
                        });
                    })
                    ->paginate(15);
            } else {
                // Selain superadmin tidak boleh lihat admin
                $users = User::with('roles')
                    ->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'superadmin');
                    })
                    ->paginate(15);
            }

            return response()->json([
                "status" => true,
                "message" => "List Semua User",
                "data" => [
                    'Users' => $users,
                    'total' => $users->count()
                ],
                'pagination' => [
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                ]

            ], 200);
        } catch (\Exception $e) {
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
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'no_telp' => 'nullable|string|max:20',
                'tanggal_lahir' => 'nullable|date',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,name',
            ]);

            $currentUser = auth()->user();
            $currentRole = $currentUser->roles->first()->name ?? null;

            if ($currentRole === 'admin') {
                $invalidRoles = collect($request->roles)->filter(function ($role) {
                    return $role !== 'user';
                });

                if ($invalidRoles->isNotEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Admin hanya bisa menetapkan role "user"',
                    ], 403);
                }
            }


            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_telp' => $request->no_telp,
                'email_verified_at' => Carbon::now(),
            ]);

            if ($request->filled('roles')) {
                $user->assignRole($request->roles);
            }

            DB::commit();

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
            $currentUser = auth()->user();
            $currentRole = $currentUser->roles->first()->name ?? null;

            $user = User::with('roles')->findOrFail($id);

            $targetRole = $user->roles->first()->name ?? null;

            if ($currentRole === 'superadmin') {
                return response()->json([
                    "status" => true,
                    "message" => "Detail User",
                    "data" => [
                        'user' => $user,
                    ],
                ], 200);
            }

            if ($currentRole === 'admin') {
                // Admin hanya bisa lihat admin dan user
                if (in_array($targetRole, ['admin', 'user'])) {
                    return response()->json([
                        "status" => true,
                        "message" => "Detail User",
                        "data" => [
                            'user' => $user,
                        ],
                    ], 200);
                } else {
                    return response()->json([
                        "status" => false,
                        "message" => "Anda tidak memiliki akses untuk melihat user ini",
                    ], 403);
                }
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan",
                "error" => $e->getMessage(),
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
        try {
            $currentUser = auth()->user();
            $currentRole = $currentUser->roles->first()->name ?? null;

            $user = User::with('roles')->findOrFail($id);
            $targetRole = $user->roles->first()->name ?? null;

            // Cek hak akses
            if ($currentRole === 'admin' && in_array($targetRole, ['admin', 'superadmin'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak memiliki izin untuk memperbarui user ini',
                ], 403);
            }

            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'no_telp' => 'nullable|string|max:20',
                'tanggal_lahir' => 'nullable|date',
                'roles' => 'array',
                'roles.*' => 'exists:roles,name',
            ]);

            $updateData = $request->only(['name', 'email','jenis_kelamin', 'no_telp']);
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Hanya superadmin yang bisa ubah role
            if ($request->has('roles')) {
                if ($currentRole === 'superadmin') {
                    $user->syncRoles($request->roles);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Hanya superadmin yang dapat mengubah peran (role)',
                    ], 403);
                }
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
        try {
            $currentUser = auth()->user();
            $currentRole = $currentUser->roles->first()->name ?? null;

            $user = User::with('roles')->findOrFail($id);
            $targetRole = $user->roles->first()->name ?? null;

            // Cek hak akses admin
            if ($currentRole === 'admin' && in_array($targetRole, ['admin', 'superadmin'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus user ini',
                ], 403);
            }

            $user->delete();

            return response()->json([
                "status" => true,
                "message" => "User Berhasil Dihapus",
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "User Tidak Ditemukan atau Terjadi Kesalahan",
                "error" => $e->getMessage(),
            ], 404);
        }
    }

}
