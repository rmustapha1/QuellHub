<?php
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\HostelsController;
use Illuminate\Support\Facades\Route;

#Public Routes
Route::group(['prefix' => 'v1', 'middleware' => 'api_auth'], function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/hostels', [HostelsController::class, 'hostels']);
    Route::get('/hostel/{id}', [HostelsController::class, 'getSingleHostel']);
    Route::get('/schools', [HostelsController::class, 'getSchools']);
    Route::get('/school/{id}', [HostelsController::class, 'getSingleSchool']);
    Route::post('/book', [HostelsController::class, 'bookHostel']);
    Route::post('/validate-booking', [HostelsController::class, 'validateBooking']);
    Route::post('/verify-number', [HostelsController::class, 'verifyNumber']);
    Route::post('/confirm-otp', [HostelsController::class, 'confirmOTP']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/reset-rooms', [HostelsController::class, 'resetRooms']);
    Route::post('/add-bookmark', [HostelsController::class, 'addBookmark']);
    Route::post('/get-past-bookings', [HostelsController::class, 'pastBookings']);
    Route::post('/get-bookmarks', [HostelsController::class, 'getBookmarks']);
    Route::post('/delete-bookmark', [HostelsController::class, 'deleteBookmark']);
    Route::post('/profile-update', [AuthController::class, 'profileUpdate']);

    //    Route::Post('/auth/password-reset/phone', [AuthController::class, 'password_reset_phone']);
//    Route::get('/auth/secret-questions', [AuthController::class, 'secret_questions']);
});
