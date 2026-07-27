<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Controller Pengguna (User)
 * Mengelola data pengguna: menampilkan, menambah, mengubah, dan menghapus pengguna.
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        // Ambil semua pengguna, urutkan berdasarkan terbaru
        $users = User::orderBy('created_at', 'desc')->get();
        return view('users', compact('users'));
    }

    /**
     * Menambah pengguna baru.
     * Validasi input, hash password, lalu simpan ke database.
     */
    public function store(Request $request)
    {
        // Validasi input pengguna
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/@jofresh\.com$/i'],
            'password' => ['required', 'string', 'min:8', 'regex:/[0-9]/'],
            'role' => 'required|string|in:Kasir,Admin,Superadmin,Owner',
        ], [
            'email.regex' => 'Email pengguna harus menggunakan domain @jofresh.com.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal 1 angka.',
        ]);

        // Simpan pengguna baru ke database dengan password ter-hash
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Mengubah data pengguna yang sudah ada.
     * Password hanya di-update jika diisi.
     */
    public function update(Request $request, string $id)
    {
        // Cari pengguna berdasarkan ID
        $user = User::findOrFail($id);

        // Validasi input pengguna
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id, 'regex:/@jofresh\.com$/i'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[0-9]/'],
            'role' => 'required|string|in:Kasir,Admin,Superadmin,Owner',
        ], [
            'email.regex' => 'Email pengguna harus menggunakan domain @jofresh.com.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal 1 angka.',
        ]);

        // Update data pengguna
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Menghapus pengguna berdasarkan ID.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
