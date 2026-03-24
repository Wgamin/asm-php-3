<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Đừng quên dòng này
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // 1. Lấy dữ liệu từ DB
        $users = User::latest()->paginate(10); 

        // 2. Truyền biến sang view bằng hàm compact()
        return view('admin.users.index', compact('users'));
    }
}