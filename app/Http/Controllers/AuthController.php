<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLogin() {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Phân hướng sau khi đăng nhập
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    // Hiển thị form đăng ký
    public function showRegister() {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', // Mặc định là user thường
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công!');
    }

    // Đăng xuất
    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    // --- PHẦN BỔ SUNG DÀNH RIÊNG CHO ADMIN ---

    // 1. Hiển thị form đăng nhập Admin
    public function showAdminLogin() {
        return view('admin.auth.login'); // Tạo view này trong admin/auth/
    }

    // 2. Xử lý đăng nhập Admin
    public function adminLogin(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Kiểm tra: Nếu là admin thì mới cho vào dashboard
            if (auth()->user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            // Nếu không phải admin, đăng xuất ngay lập tức
            Auth::logout();
            return back()->withErrors(['email' => 'Tài khoản này không có quyền truy cập quản trị.']);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    // 3. Hiển thị form đăng ký Admin (Nếu bạn muốn tự tạo admin mới)
    public function showAdminRegister() {
        return view('admin.auth.register');
    }

    // 4. Xử lý đăng ký Admin
    public function adminRegister(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin', // GÁN CỨNG QUYỀN ADMIN
        ]);

        return redirect()->route('admin.login')->with('success', 'Tạo tài khoản Admin thành công!');
    }

    public function storeAdmin(Request $request) 
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin', // Xác định đây là tài khoản quản trị
    ]);

    return redirect()->back()->with('success', 'Đã tạo tài khoản Admin mới thành công!');
}
}