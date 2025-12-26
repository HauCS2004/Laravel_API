<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
//Route::redirect('/', '/home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/{any}', function () {

    return view('home');
})->where('any', '.*');

Route::get('/test-email', function () {
    try {
        Mail::raw('Đây là email test hệ thống!', function ($message) {
            $message->to('test@example.com')
                    ->subject('Test Mailtrap');
        });
        return 'Gửi mail thành công! Cấu hình ngon lành.';
    } catch (\Exception $e) {
        return 'Lỗi rồi: ' . $e->getMessage();
    }
});
// routes/web.php

// ... các route cũ giữ nguyên

// Thêm đoạn này:
Route::get('/nap-du-lieu-bi-mat', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]); // Xóa sạch làm lại bảng (cho chắc)
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]); // Nạp dữ liệu mẫu
    return 'Đã nạp dữ liệu thành công! Vào trang chủ xem hàng đi sếp!';
});
