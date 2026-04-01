<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite; // THÊM DÒNG NÀY
use Illuminate\Support\Str; // THÊM DÒNG NÀY ĐỂ TẠO MẬT KHẨU ẢO

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
    // --- BỔ SUNG ĐĂNG NHẬP GOOGLE ---

    // 1. Chuyển hướng người dùng sang trang đăng nhập của Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Xử lý thông tin Google trả về sau khi đăng nhập thành công
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin user từ Google
            $googleUser = Socialite::driver('google')->user();

            // Kiểm tra xem email này đã tồn tại trong DB chưa
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Nếu đã có tài khoản (có thể tạo bằng tay trước đó), cập nhật thêm google_id và đăng nhập
                $user->update(['google_id' => $googleUser->getId()]);
                Auth::login($user);
            } else {
                // Nếu chưa có, tạo tài khoản mới tự động
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)), // Đặt mật khẩu ảo cho an toàn
                    'role' => 'user' // Mặc định là khách hàng
                ]);
                Auth::login($user);
            }

            // Chuyển hướng về trang chủ sau khi thành công
            return redirect('/')->with('success', 'Đăng nhập bằng Google thành công!');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Có lỗi xảy ra khi đăng nhập bằng Google. Vui lòng thử lại!']);
        }
    }
}