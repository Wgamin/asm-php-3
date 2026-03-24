<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // 1. Kiểm tra đã đăng nhập chưa
    // 2. Kiểm tra role có phải admin không
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Nếu không phải admin, đá về trang chủ hoặc báo lỗi
    return redirect('/')->with('error', 'Bạn không có quyền truy cập khu vực này.');
}
}
