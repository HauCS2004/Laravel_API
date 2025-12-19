<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    // Hàm tạo URL thanh toán
    public function createPayment(Request $request)
    {
        // 1. Lấy thông tin đơn hàng từ request (gửi order_id lên)
        $request->validate(['order_id' => 'required|exists:orders,id']);
        
        $order = Order::find($request->order_id);
        
        // Kiểm tra xem đơn đã thanh toán chưa
        if ($order->status === 'paid') {
            return response()->json(['message' => 'Đơn này trả tiền rồi fen ơi!'], 400);
        }

        // 2. Cấu hình tham số VNPAY
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURN_URL');
        
        $vnp_TxnRef = $order->id; // Mã đơn hàng của mình
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $order->total_price * 100; // VNPAY tính bằng user, phải nhân 100
        $vnp_Locale = "vn";
        $vnp_IpAddr = $request->ip();
        
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
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // 3. Sắp xếp tham số theo A-Z (Bắt buộc)
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

        // 4. Tạo URL thanh toán
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Trả về link thanh toán cho Frontend/Postman
        return response()->json([
            'message' => 'Tạo link thanh toán thành công',
            'payment_url' => $vnp_Url
        ]);
    }
    // Hàm xử lý khi người dùng thanh toán xong và quay về web
    public function vnpayReturn(Request $request)
    {
        // 1. Lấy dữ liệu VNPAY trả về
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']); // Loại bỏ mã hash để tính toán lại
        
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

        // 2. Kiểm tra tính toàn vẹn dữ liệu (tránh hacker giả mạo)
        if ($secureHash == $vnp_SecureHash) {
            // Check xem giao dịch có thành công không (Code 00 là thành công)
            if ($request->vnp_ResponseCode == '00') {
                
                // Lấy ID đơn hàng từ vnp_TxnRef
                $orderId = $request->vnp_TxnRef;
                $order = Order::find($orderId);

                if ($order) {
                    // Cập nhật trạng thái đơn hàng
                    $order->status = 'paid'; // Đổi thành đã thanh toán
                    $order->save();

                    return response()->json([
                        'message' => 'Thanh toán thành công! Đơn hàng đã được cập nhật.',
                        'data' => $request->all()
                    ]);
                } else {
                    return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
                }

            } else {
                return response()->json([
                    'message' => 'Giao dịch không thành công hoặc bị hủy',
                    'error_code' => $request->vnp_ResponseCode
                ], 400);
            }
        } else {
            return response()->json(['message' => 'Chữ ký không hợp lệ! (Có thể dữ liệu bị thay đổi)'], 400);
        }
    }
}