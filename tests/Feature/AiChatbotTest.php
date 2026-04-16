<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatbotController extends Controller
{
    private function removeAccent($str)
    {
        $str = strtolower($str);
        $unicode = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ'
        ];
        foreach ($unicode as $na => $ac) {
            $str = preg_replace("/($ac)/i", $na, $str);
        }
        return $str;
    }

    // 👉 mapping từ khóa đồng nghĩa
    private function mapKeywords($msg)
    {
        $map = [
            'ho tran' => 'hat de cuoi',
            'pistachio' => 'hat de cuoi'
        ];

        foreach ($map as $key => $value) {
            if (str_contains($msg, $key)) {
                $msg .= ' ' . $value;
            }
        }

        return $msg;
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $raw = trim($request->message);
        $msg = $this->removeAccent($raw);

        // 👉 map từ khóa
        $msg = $this->mapKeywords($msg);

        // ===== SEARCH LUÔN ƯU TIÊN =====
        $keywords = array_filter(explode(' ', $msg));

        $products = Product::all()->filter(function ($p) use ($keywords) {
            $name = $this->removeAccent($p->name);

            foreach ($keywords as $w) {
                if (strlen($w) >= 2 && str_contains($name, $w)) {
                    return true;
                }
            }

            return false;
        })->values()->take(5);

        // ===== 👉 CHẶN AI 100% NẾU CÓ SẢN PHẨM =====
        if ($products->count() > 0) {

            $reply = "Shop tìm thấy sản phẩm phù hợp 🍀:\n\n";

            foreach ($products as $p) {
                $reply .= "- {$p->name}\n";
                $reply .= "Giá: " . number_format($p->price) . "đ\n";

                if ($p->image) {
                    $reply .= "Hình: " . asset('storage/' . $p->image) . "\n";
                }

                $reply .= "\n";
            }

            $reply .= "Bạn cần mình tư vấn thêm không ạ 😊";

            // ❗ RETURN NGAY → KHÔNG BAO GIỜ XUỐNG AI
            return response()->json([
                'message' => [
                    'role' => 'model',
                    'message' => $reply
                ]
            ]);
        }

        // ===== AI CHỈ CHẠY KHI KHÔNG CÓ SẢN PHẨM =====
        try {
            $prompt = "Bạn là chatbot của shop nông sản. Trả lời ngắn gọn, đúng trọng tâm, tiếng Việt. Khách: $raw";

            $response = Http::withHeaders([
                'x-goog-api-key' => config('services.gemini.api_key'),
            ])->post(
                config('services.gemini.base_url') .
                '/models/' . config('services.gemini.model') . ':generateContent',
                [
                    'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]]]
            );

            if ($response->failed()) {
    return response()->json([
        'message' => [
            'role' => 'model',
            'message' => 'Dạ hệ thống đang bận, bạn thử lại sau nha 😊'
        ]
    ]);
}
        } catch (\Exception $e) {
            $reply = "Dạ hệ thống lỗi nhẹ 😥";
        }

        return response()->json([
            'message' => [
                'role' => 'model',
                'message' => $reply
            ]
        ]);
    }
}
