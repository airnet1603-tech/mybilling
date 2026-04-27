<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function index() {
        $users = User::orderBy(DB::raw("FIELD(role, 'superadmin', 'admin', 'operator')"))->when(auth()->user()->role !== 'superadmin', function($q) { return $q->where('role', '!=', 'superadmin'); })->get();
        return view('admin.users.index', compact('users'));
    }
    public function create() { return view('admin.users.create'); }
    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:superadmin,admin,operator',
        ]);
        User::create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'role'=>$request->role]);
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }
    public function edit(User $user) { return view('admin.users.edit', compact('user')); }
    public function update(Request $request, User $user) {
        $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email,'.$user->id,'role'=>'required|in:superadmin,admin,operator']);
        $data = ['name'=>$request->name,'email'=>$request->email,'role'=>$request->role];
        if ($request->filled('password')) {
            $request->validate(['password'=>'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }
    public function destroy(User $user) {
        if ($user->id === auth()->id()) return redirect()->route('users.index')->with('error', 'Tidak bisa hapus akun sendiri.');
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
