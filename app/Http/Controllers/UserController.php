<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Role yang tersedia.
     */
    private const ROLES = [
        'admin'    => 'Administrator',
        'operator' => 'Logistics Operator',
        'customer' => 'Customer',
    ];

    /**
     * Menampilkan daftar pengguna dan statistik.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalUsers    = User::count();
        $adminUsers    = User::where('role', 'admin')->count();
        $operatorUsers = User::where('role', 'operator')->count();
        $customerUsers = User::where('role', 'customer')->count();

        $roles = self::ROLES;

        return view('users.index', compact(
            'users',
            'totalUsers',
            'adminUsers',
            'operatorUsers',
            'customerUsers',
            'roles'
        ));
    }

    /**
     * Form tambah memakai modal di index.
     */
    public function create()
    {
        return redirect()->route('users.index');
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys(self::ROLES))],
        ]);

        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role     = $validated['role'];
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Detail pengguna (JSON).
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Form edit memakai modal di index.
     */
    public function edit($id)
    {
        return redirect()->route('users.index');
    }

    /**
     * Memperbarui informasi pengguna.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(array_keys(self::ROLES))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Lindungi akun yang sedang dipakai agar tidak kehilangan akses admin
        if (
            (int) auth()->id() === (int) $user->id &&
            $user->role === 'admin' &&
            $validated['role'] !== 'admin'
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Role administrator pada akun sendiri tidak dapat diturunkan.');
        }

        // Lindungi administrator terakhir
        if (
            $user->role === 'admin' &&
            $validated['role'] !== 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Administrator terakhir tidak dapat diturunkan rolenya.');
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ((int) auth()->id() === (int) $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Kamu tidak dapat menghapus akun yang sedang digunakan.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Administrator terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
