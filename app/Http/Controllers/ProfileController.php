<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Order; // Thêm dòng này để code sạch hơn

class ProfileController extends Controller
{
    /**
     * Hiển thị hồ sơ cá nhân kèm danh sách đơn hàng
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy danh sách đơn hàng của user, sắp xếp mới nhất lên đầu
        $orders = Order::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Đảm bảo view trỏ đúng vào 'client.profile' như bạn mong muốn
        return view('profile.index', compact('user', 'orders'));
    }

    /**
     * Cập nhật thông tin hồ sơ
     */
    public function update(Request $request) 
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
}