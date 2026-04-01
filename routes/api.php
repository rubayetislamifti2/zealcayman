<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, FAQController, ProfileController, SettingsController, SupportController,NotificationController};

Route::prefix('auth')->group(function () {
    Route::post('register',[AuthController::class,'register']);
    Route::post('verify-otp',[AuthController::class,'verifyOtp']);
    Route::post('resend-otp',[AuthController::class,'resendOtp']);
    Route::post('forget-password',[AuthController::class,'forgetPassword']);
    Route::post('reset-password',[AuthController::class,'resetPassword']);
    Route::post('login',[AuthController::class,'login']);
    Route::post('logout',[AuthController::class,'logout'])->middleware(['jwt.auth']);
});


Route::middleware(['jwt.auth'])->group(function () {

    Route::prefix('notifications')->group(function () {
        Route::get('/',              [NotificationController::class, 'getNotifications']);
        Route::get('unread',         [NotificationController::class, 'unreadNotification']);
        Route::get('unread/count',   [NotificationController::class, 'unreadCount']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::post('{id}/read',     [NotificationController::class, 'markAsRead']);
    });

    Route::prefix('settings')->group(function () {
        Route::post('update-profile',[ProfileController::class,'updateProfile']);
        Route::post('update-profile/image',[ProfileController::class,'updateImage']);
        Route::post('change-password',[AuthController::class,'changePassword']);
        Route::get('about-us',[SettingsController::class,'getAboutUs']);
        Route::post('about-us',[SettingsController::class,'aboutUs']);

        Route::get('terms-condition',[SettingsController::class,'getTermsCondition']);
        Route::post('terms-condition',[SettingsController::class,'termsCondition']);

        Route::get('privacy-policy',[SettingsController::class,'getPrivacyPolicy']);
        Route::post('privacy-policy',[SettingsController::class,'privacyPolicy']);

        Route::post('createSupport',[SupportController::class,'createSupport']);
        Route::get('getSupport',[SupportController::class,'getSupports']);
        Route::get('updateSupport/{support_id}',[SupportController::class,'updateSupport']);
        Route::get('replySupport/{support_id}',[SupportController::class,'replySupport']);
        Route::get('deleteSupport/{support_id}',[SupportController::class,'deleteSupport']);

        Route::get('faq',[FAQController::class,'getFAQ']);
        Route::post('faq',[FAQController::class,'createFaq']);
        Route::post('faq/{id}',[FAQController::class,'updateFaq']);
        Route::delete('faq/{id}',[FAQController::class,'deleteFaq']);
    });
});
