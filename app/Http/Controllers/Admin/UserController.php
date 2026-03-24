<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Hiển thị danh sách khách hàng
    public function index()
    {
        // Chỉ lấy những người dùng có role là 'user' (khách hàng)
        $users = User::where('role', 'user')
                     ->latest()
                     ->paginate(10); 

        return view('admin.users.index', compact('users'));
    }

    // Lưu khách hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Mặc định luôn là khách hàng
        ]);

        return back()->with('success', 'Thêm khách hàng thành công!');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    // Cập nhật thông tin khách hàng
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Cập nhật thành công!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Xóa khách hàng
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Đã xóa khách hàng!');
    }
}