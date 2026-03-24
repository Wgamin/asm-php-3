<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Chỉ lấy 5 khách hàng mới nhất (Lọc theo role là 'user')
        $latestUsers = User::where('role', 'user')
                           ->latest()
                           ->limit(5)
                           ->get();

        // 2. Tạm thời gán mảng rỗng cho đơn hàng
        $latestOrders = []; 

        return view('admin.dashboard', compact('latestUsers', 'latestOrders'));
    }
}