<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\CloseDateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPackageDiscountController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [PageController::class, 'onboarding'])->name('onboarding.index');
    Route::post('/onboarding/save', [OnboardingController::class, 'save'])->name('onboarding.save');
    Route::post('/payment/policy/save', [OnboardingController::class, 'paymentPolicySave'])->name('policy.save');
    Route::get('/schedule', [HomeController::class, 'schedule'])->name('schedule.page');
    Route::get('/payment/{id}', [HomeController::class, 'payment'])->name('payment.page');
    Route::get('/buy/package/viacoin/{id}', [HomeController::class, 'redeemCoin'])->name('redeem.coin');
    Route::post('/payment', [HomeController::class, 'paymentSubmit'])->name('payment.submit');
    Route::get('/history', [HomeController::class, 'history'])->name('history.page');
    Route::get('/join/class/{id}', [HomeController::class, 'joinClass'])->name('join.class');
    Route::get('/remove/class/{id}', [HomeController::class, 'removeClass'])->name('remove.class');
    Route::get('/edit/profile', [PageController::class, 'editProfile'])->name('edit.profile');
    Route::post('/update/user/info', [PageController::class, 'updateProfile'])->name('update.profile');
    Route::post('/update/avatar', [PageController::class, 'updateAvatar'])->name('update.avatar');
    Route::post('/change/password', [PageController::class, 'changePassword'])->name('update.password');
    Route::post('/notifications/read-all', [PageController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [PageController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/post/comment', [CommentController::class, 'store'])->name('post.comment');
    // Package သီးသန့်ဝယ်ထားသော User များကိုပြရန်
    Route::get('/users/with-packages', [UserController::class, 'usersWithPackages'])->name('user.with_packages');

    // သက်ဆိုင်ရာ User ၏ Package အသေးစိတ်ကိုကြည့်ရန် (Route အဟောင်းရှိလျှင် ဖျက်ပြီး ဤစာကြောင်းကိုသာ ထည့်ပါ)
    Route::get('/user/{id}/package/index', [UserController::class, 'userPackageDetails'])->name('user.package.details');
    Route::get('/my-class-history', [HomeController::class, 'myClassHistory'])->name('schedule.page');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/buy/package/{id}', [PackageController::class, 'buyPackgeIndex'])->name('buy.package-page');
    Route::post('/buy/package', [PackageController::class, 'buyPackge'])->name('buy.package');

    Route::get('/join/class/for/user/{id}', [ClassScheduleController::class, 'joinClassForUser'])->name('join.class-for-user');
    Route::post('/join/class/for/user', [ClassScheduleController::class, 'joinClassForUserSubmit'])->name('join.class-for-user-submit');
    Route::get('/check-class-eligibility', [ClassScheduleController::class, 'checkClassEligibility'])->name('check.class.eligibility');

    Route::middleware(['auth', 'role:Admin|Instructor|Receptionist'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


        // Categories  Routes
        // ==========================================
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        // ==========================================

        // Instructor Management Routes
        // ==========================================
        Route::get('/instructors', [InstructorController::class, 'index'])->name('instructors.index');
        Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
        Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');
        Route::get('/instructors/{instructor}/edit', [InstructorController::class, 'edit'])->name('instructors.edit');
        Route::put('/instructors/{instructor}', [InstructorController::class, 'update'])->name('instructors.update');
        Route::delete('/instructors/{instructor}', [InstructorController::class, 'destroy'])->name('instructors.destroy');

        // Workshop Management Routes
        // ==========================================
        Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
        Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
        Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
        Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
        Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');

        Route::get('/get/instructor/earnings', [EarningsController::class, 'getInstructorEarnings'])->name('instructor.earnings');
        Route::put('/instructors/{instructor}/earnings', [EarningsController::class, 'updateInstructorEarnings'])->name('instructors.earnings.update');
    });
    Route::middleware(['auth', 'role:Admin'])->group(function () {

        // ==========================================
        // Payment  Routes
        // ==========================================
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        Route::get('/close-dates', [CloseDateController::class, 'index'])->name('close_dates.index');
        Route::post('/close-dates', [CloseDateController::class, 'store'])->name('close_dates.store');
        Route::put('/close-dates/{closeDate}', [CloseDateController::class, 'update'])->name('close_dates.update');
        Route::delete('/close-dates/{closeDate}', [CloseDateController::class, 'destroy'])->name('close_dates.destroy');

        Route::get('/comments/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroyAdminComment'])->name('comments.destroy');
        Route::patch('/comments/approve/{comment}', [CommentController::class, 'approveComment'])->name('comments.approve.update');

        // ==========================================
        // Class Schedule Management Routes
        // ==========================================
        Route::get('/class-schedules', [ClassScheduleController::class, 'index'])->name('class_schedules.index');
        Route::post('/class-schedules', [ClassScheduleController::class, 'store'])->name('class_schedules.store');
        Route::put('/class-schedules/{classSchedule}', [ClassScheduleController::class, 'update'])->name('class_schedules.update');
        Route::delete('/class-schedules/{classSchedule}', [ClassScheduleController::class, 'destroy'])->name('class_schedules.destroy');
        Route::put('/class/cancel/{classSchedule}', [ClassScheduleController::class, 'cancel'])->name('class_schedules.cancel');

        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::put('/gallery/{gallery}', [GalleryController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // User
        Route::controller(UserController::class)
            ->prefix('user_register')
            ->name('user_register.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/discount', 'discount')->name('discount');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}/update', 'update')->name('update');
                Route::delete('/{id}/destroy', 'destroy')->name('destroy');
                // Route::put('/{id}', 'role_update')->name('role_update');
            });
        Route::put('/user-register/{id}', [UserController::class, 'role_update'])->name('user_register.role_update');
    });

    // User Type Routes Not Used
    // Route::controller(UserTypeController::class)
    //     ->prefix('user_types')
    //     ->name('user_types.')
    //     ->group(function () {
    //         Route::get('/', 'index')->name('index');
    //         Route::post('/store', 'store')->name('store');
    //         Route::get('/{id}/edit', 'edit')->name('edit');
    //         Route::put('/{id}/update', 'update')->name('update');
    //         Route::delete('/{id}/destroy', 'destroy')->name('destroy');
    //     });

    // User Register Routes (Pointed to new UserController)
    Route::controller(UserController::class)
        ->prefix('user_register')
        ->name('user_register.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}/update', 'update')->name('update');
            Route::delete('/{id}/destroy', 'destroy')->name('destroy');
        });



    // ==========================================
    // Instructor Management Routes
    // ==========================================
    Route::get('/instructors', [InstructorController::class, 'index'])->name('instructors.index');
    Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
    Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');
    Route::get('/instructors/{instructor}/edit', [InstructorController::class, 'edit'])->name('instructors.edit');
    Route::put('/instructors/{instructor}', [InstructorController::class, 'update'])->name('instructors.update');
    Route::delete('/instructors/{instructor}', [InstructorController::class, 'destroy'])->name('instructors.destroy');



    // ==========================================
    // Class Schedule Management Routes
    // ==========================================
    Route::get('/class-schedules', [ClassScheduleController::class, 'index'])->name('class_schedules.index');
    Route::post('/class-schedules', [ClassScheduleController::class, 'store'])->name('class_schedules.store');
    Route::put('/class-schedules/{classSchedule}', [ClassScheduleController::class, 'update'])->name('class_schedules.update');
    Route::delete('/class-schedules/{classSchedule}', [ClassScheduleController::class, 'destroy'])->name('class_schedules.destroy');
    Route::put('/class-schedules/{classSchedule}/instructors', [ClassScheduleController::class, 'updateInstructors'])->name('schedules.update-instructors');
    Route::get('/class_schedules_list', [ClassScheduleController::class, 'class_schedules_list'])->name('class_schedules_list');



    // Attendances
    Route::get('/record', [AttendanceController::class, 'record'])->name('attendances.record');
    Route::get('/attendance/data', [AttendanceController::class, 'attendanceData'])->name('attendances.data');
    Route::get('/attendance/day-details', [AttendanceController::class, 'attendanceDayDetails']);
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/inTime', [AttendanceController::class, 'inTime'])->name('attendances.inTime');
    Route::post('/attendances/client/inTime', [AttendanceController::class, 'ClientinTime'])->name('attendances.ClientinTime');
    Route::put('/admin/approve/{instructor}', [AttendanceController::class, 'adminApprove'])->name('attendances.adminApprove');
    // Route::post('/attendances/outTime', [AttendanceController::class, 'outTime'])->name('attendances.outTime');

    // ==========================================
    // Payment  Routes
    // ==========================================
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('/manage/purchases', [PaymentController::class, 'managePurchases'])->name('purchases.manage.page');
    Route::patch('/transactions/{id}/status', [PaymentController::class, 'updateStatus'])->name('transactions.update-status');
    Route::delete('/transactions/{id}', [PaymentController::class, 'destroyPurchase'])->name('transactions.destroy');

    // ==========================================
    // Categories  Routes
    // ==========================================
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // ==========================================
    // Package  Routes
    // ==========================================
    Route::get('/package', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/package', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/package/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/package/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/get-classes-by-category/{categoryId}', [PackageController::class, 'getClassesByCategory'])->name('packages.get-classes-by-category');

    Route::get('/give/discount/{id}', [UserPackageDiscountController::class, 'create'])->name('packages.give-discount');
    Route::post('/give/discount', [UserPackageDiscountController::class, 'store'])->name('discount.store');
    Route::delete('/user/{userId}/package/{packageId}/remove', [UserPackageDiscountController::class, 'destroy'])->name('discount.remove');
    Route::get('/user/{userId}/package/index', [UserPackageDiscountController::class, 'discountIndex'])->name('discount.index');
    Route::put('/user/{userId}/package/{packageId}/update', [UserPackageDiscountController::class, 'update'])->name('discount.update');


    Route::get('/bookings', [BookingController::class, 'getBookings'])->name('bookings.index');
    Route::get('/waitlist', [BookingController::class, 'getWaitList'])->name('waitlist.index');
    Route::patch('/bookings/status/{id}', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::get('/cancel/booking/{id}', [BookingController::class, 'cancelBooking'])->name('bookings.cancel-booking');
    Route::post('/admin/booking/store', [BookingController::class, 'adminBookClass'])->name('admin.booking.store');
    Route::get('/get/customer/report', [ReportController::class, 'getCustomerReport'])->name('customer.report');
    Route::get('get/customer/report/{id}', [ReportController::class, 'getCustomerPackagesAjax'])->name('customer.packages.report');
    Route::get('get/top/packages', [ReportController::class, 'getTopPackages'])->name('top.packages');
    Route::get('get/monthly/sales', [ReportController::class, 'getMonthlySales'])->name('montly.sales');

    Route::get('/get/instructor/report', [ReportController::class, 'getInstructorReport'])->name('instructor.report');
    Route::get('get/instructor/report/{id}', [ReportController::class, 'getInstructorPackagesAjax'])->name('instructor.packages.report');
    Route::get('get/instructor/it/report', [ReportController::class, 'getInstructorReports'])->name('instructor.it.reports');

});

require __DIR__ . '/auth.php';
