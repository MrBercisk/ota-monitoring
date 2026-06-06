<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function datatable()
    {
        $users = User::select(['id', 'name', 'email', 'role', 'created_at']);
 
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_badge', function ($user) {
                if ($user->role === 'admin') {
                    return '<span class="badge bg-label-danger rounded-pill">Admin</span>';
                }
                return '<span class="badge bg-label-info rounded-pill">User</span>';
            })
            ->addColumn('created_fmt', function ($user) {
                return $user->created_at->format('d M Y');
            })
            ->addColumn('aksi', function ($user) {
                $edit = '<a href="' . route('user.edit', $user) . '"
                            class="btn btn-sm btn-icon btn-outline-primary">
                            <i class="ri ri-edit-line"></i>
                        </a>';

                $delete = '<button type="button"
                                class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                data-url="' . route('user.destroy', $user) . '"
                                data-message="User ' . e($user->name) . ' akan dihapus permanen!">
                                <i class="ri ri-delete-bin-line"></i>
                        </button>';

                return $edit . ' ' . $delete;
            })
            ->rawColumns(['role_badge', 'aksi'])
            ->make(true);
    }


    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('user.index')
                         ->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:admin,user',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')
                         ->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return request()->expectsJson()
            ? response()->json(['message' => 'User ' . $user->name . ' berhasil dihapus.'])
            : redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}