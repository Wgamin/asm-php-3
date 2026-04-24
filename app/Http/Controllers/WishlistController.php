<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Hiển thị danh sách yêu thích
    public function index()
    {
        return redirect()->route('profile', ['tab' => 'wishlist']);
    }

    // Thêm hoặc xóa sản phẩm khỏi yêu thích (Toggle)

    public function toggle($id)
    {
        // 1. Kiểm tra đăng nhập
        if (!auth()->check()) {
            return response_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        
        // 2. Sử dụng syncWithoutDetaching hoặc toggle
        // toggle() sẽ tự thêm nếu chưa có, xóa nếu đã có
        $result = $user->wishlists()->toggle($id);

        // 3. Kiểm tra xem là vừa THÊM hay vừa XÓA
        $status = count($result['attached']) > 0 ? 'added' : 'removed';

        return response()->json([
            'status' => $status,
            'message' => $status == 'added' ? 'Đã thêm vào yêu thích' : 'Đã xóa'
        ]);
    }
}