<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        // Hanya admin yang bisa mengakses
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with('supervisor')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4',
            'role' => 'required|in:staff,atasan,supervisor,admin',
            'supervisor_id' => 'nullable|exists:users,id',
            'coordinator_id' => 'nullable|exists:users,id',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|unique:users',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'coordinator_id' => $validated['coordinator_id'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'nik' => $validated['nik'] ?? null,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user->load('supervisor'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:staff,atasan,supervisor,admin',
            'supervisor_id' => 'nullable|exists:users,id',
            'coordinator_id' => 'nullable|exists:users,id',
            'jabatan' => 'nullable|string|max:255',
            'nik' => 'nullable|string|unique:users,nik,' . $id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->load('supervisor'),
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
