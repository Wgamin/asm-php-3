<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{   
    public function createPayment(Request $request)
    {
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        // Lấy dữ liệu động từ OrderController truyền sang
        $vnp_TxnRef = $request->order_id; // Mã đơn hàng trong database
        $vnp_Amount = $request->total_amount * 100; // Tổng tiền đơn hàng (VNPAY yêu cầu nhân 100)
        $vnp_OrderInfo = 'Thanh toan don hang ' . $vnp_TxnRef . ' - TheKing Perfume'; // Viết không dấu để tránh lỗi font
        $vnp_OrderType = 'billpayment';
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip(); // Dùng hàm chuẩn của Laravel lấy IP

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        // Sắp xếp dữ liệu theo thứ tự a-z trước khi tạo mã băm
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng người dùng sang trang thanh toán của VNPAY
        return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công: Cập nhật trạng thái database orders thành "Đã thanh toán"
                return redirect('/')->with('success', 'Thanh toán thành công!');
            } else {
                // Giao dịch bị hủy hoặc lỗi
                return redirect('/checkout')->with('error', 'Thanh toán thất bại!');
            }
        } else {
            return redirect('/checkout')->with('error', 'Chữ ký không hợp lệ!');
        }
    }

    
}