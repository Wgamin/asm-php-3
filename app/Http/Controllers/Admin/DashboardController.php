<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
// Tạm thời chưa dùng Order thì không cần import hoặc cứ để đó cũng được
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Vẫn lấy 5 thành viên mới nhất (Vì bảng users thường có sẵn)
        $latestUsers = User::latest()->limit(5)->get();

        // 2. Tạm thời gán mảng rỗng cho đơn hàng để không bị lỗi SQL
        $latestOrders = []; 

        return view('admin.dashboard', compact('latestUsers', 'latestOrders'));
    }
}