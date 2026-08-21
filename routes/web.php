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


/*
|--------------------------------------------------------------------------
| CUSTOMER / AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
|
| These routes are available to normal authenticated users.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Onboarding
    |--------------------------------------------------------------------------
    */

    Route::get('/onboarding', [
        PageController::class,
        'onboarding'
    ])->name('onboarding.index');

    Route::post('/onboarding/save', [
        OnboardingController::class,
        'save'
    ])->name('onboarding.save');

    Route::post('/payment/policy/save', [
        OnboardingController::class,
        'paymentPolicySave'
    ])->name('policy.save');


    /*
    |--------------------------------------------------------------------------
    | Customer Home / Schedule
    |--------------------------------------------------------------------------
    */

    Route::get('/schedule', [
        HomeController::class,
        'schedule'
    ])->name('schedule.page');

    Route::get('/payment/{id}', [
        HomeController::class,
        'payment'
    ])->name('payment.page');

    Route::get('/buy/package/viacoin/{id}', [
        HomeController::class,
        'redeemCoin'
    ])->name('redeem.coin');

    Route::post('/payment', [
        HomeController::class,
        'paymentSubmit'
    ])->name('payment.submit');

    Route::get('/history', [
        HomeController::class,
        'history'
    ])->name('history.page');

    Route::get('/join/class/{id}', [
        HomeController::class,
        'joinClass'
    ])->name('join.class');

    Route::get('/remove/class/{id}', [
        HomeController::class,
        'removeClass'
    ])->name('remove.class');

    Route::get('/my-class-history', [
        HomeController::class,
        'myClassHistory'
    ])->name('my.class.history');


    /*
    |--------------------------------------------------------------------------
    | Customer Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/edit/profile', [
        PageController::class,
        'editProfile'
    ])->name('edit.profile');

    Route::post('/update/user/info', [
        PageController::class,
        'updateProfile'
    ])->name('update.profile');

    Route::post('/update/avatar', [
        PageController::class,
        'updateAvatar'
    ])->name('update.avatar');

    Route::post('/change/password', [
        PageController::class,
        'changePassword'
    ])->name('update.password');


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::post('/notifications/read-all', [
        PageController::class,
        'markAllRead'
    ])->name('notifications.readAll');

    Route::post('/notifications/{id}/read', [
        PageController::class,
        'markAsRead'
    ])->name('notifications.markAsRead');


    /*
    |--------------------------------------------------------------------------
    | Customer Comments
    |--------------------------------------------------------------------------
    */

    Route::post('/post/comment', [
        CommentController::class,
        'store'
    ])->name('post.comment');


    /*
    |--------------------------------------------------------------------------
    | Customer User Package
    |--------------------------------------------------------------------------
    */

    Route::get('/users/with-packages', [
        UserController::class,
        'usersWithPackages'
    ])->name('user.with_packages');

    Route::get('/user/{id}/package/index', [
        UserController::class,
        'userPackageDetails'
    ])->name('user.package.details');

    Route::post('/user-register/reset-password', [
        UserController::class,
        'resetPassword'
    ])->name('user_register.reset_password');


    /*
    |--------------------------------------------------------------------------
    | Customer Class Eligibility
    |--------------------------------------------------------------------------
    */

    Route::get('/check-class-eligibility', [
        UserController::class,
        'checkClassEligibility'
    ])->name('check.class.eligibility');


    /*
    |--------------------------------------------------------------------------
    | Customer Buy Package
    |--------------------------------------------------------------------------
    */

    Route::get('/buy/package/{id}', [
        PackageController::class,
        'buyPackgeIndex'
    ])->name('buy.package-page');

    Route::post('/buy/package', [
        PackageController::class,
        'buyPackge'
    ])->name('buy.package');


    /*
    |--------------------------------------------------------------------------
    | Customer Join Class For User
    |--------------------------------------------------------------------------
    */

    Route::get('/join/class/for/user/{id}', [
        ClassScheduleController::class,
        'joinClassForUser'
    ])->name('join.class-for-user');

    Route::post('/join/class/for/user', [
        ClassScheduleController::class,
        'joinClassForUserSubmit'
    ])->name('join.class-for-user-submit');


    /*
    |--------------------------------------------------------------------------
    | Customer Class Eligibility
    |--------------------------------------------------------------------------
    */

    Route::get('/check-class-eligibility/class', [
        ClassScheduleController::class,
        'checkClassEligibility'
    ])->name('check.class.eligibility.class');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});



/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
|
| Only users with dashboard.view permission.
|
*/

Route::get('/dashboard', [
    DashboardController::class,
    'index'
])
    ->middleware(['auth', 'permission:dashboard.view'])
    ->name('dashboard');



/*
|--------------------------------------------------------------------------
| WORKSHOP
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/

Route::middleware([
    'auth',
    'permission:workshops.view'
])->group(function () {

    Route::get('/workshops', [
        WorkshopController::class,
        'index'
    ])->name('workshops.index');

});
// Class Approval & Cancel
Route::put('/attendances/class-approve/{classId}', [App\Http\Controllers\AttendanceController::class, 'classApprove'])->name('attendances.classApprove');
Route::put('/attendances/class-approve-cancel/{classId}', [App\Http\Controllers\AttendanceController::class, 'cancelClassApprove'])->name('attendances.cancelClassApprove');

// Admin Approval & Cancel
Route::put('/attendances/admin-approve/{instructorId}', [App\Http\Controllers\AttendanceController::class, 'adminApprove'])->name('attendances.adminApprove');
Route::put('/attendances/admin-approve-cancel/{instructorId}', [App\Http\Controllers\AttendanceController::class, 'cancelAdminApprove'])->name('attendances.cancelAdminApprove');

Route::middleware([
    'auth',
    'permission:workshops.manage'
])->group(function () {

    Route::post('/workshops', [
        WorkshopController::class,
        'store'
    ])->name('workshops.store');

    Route::get('/workshops/{workshop}/edit', [
        WorkshopController::class,
        'edit'
    ])->name('workshops.edit');

    Route::put('/workshops/{workshop}', [
        WorkshopController::class,
        'update'
    ])->name('workshops.update');

    Route::delete('/workshops/{workshop}', [
        WorkshopController::class,
        'destroy'
    ])->name('workshops.destroy');

});



/*
|--------------------------------------------------------------------------
| CLOSE DATES
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:close_dates.view'
])->group(function () {

    Route::get('/close-dates', [
        CloseDateController::class,
        'index'
    ])->name('close_dates.index');

});


Route::middleware([
    'auth',
    'permission:close_dates.manage'
])->group(function () {

    Route::post('/close-dates', [
        CloseDateController::class,
        'store'
    ])->name('close_dates.store');

    Route::put('/close-dates/{closeDate}', [
        CloseDateController::class,
        'update'
    ])->name('close_dates.update');

    Route::delete('/close-dates/{closeDate}', [
        CloseDateController::class,
        'destroy'
    ])->name('close_dates.destroy');

});



/*
|--------------------------------------------------------------------------
| COMMENTS / APPROVAL
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:comments.view'
])->group(function () {

    Route::get('/comments/approve', [
        CommentController::class,
        'approve'
    ])->name('comments.approve');

});


Route::middleware([
    'auth',
    'permission:comments.manage'
])->group(function () {

    Route::delete('/comments/{comment}', [
        CommentController::class,
        'destroyAdminComment'
    ])->name('comments.destroy');

    Route::patch('/comments/approve/{comment}', [
        CommentController::class,
        'approveComment'
    ])->name('comments.approve.update');

});



/*
|--------------------------------------------------------------------------
| GALLERY
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:gallery.view'
])->group(function () {

    Route::get('/gallery', [
        GalleryController::class,
        'index'
    ])->name('gallery.index');

});


Route::middleware([
    'auth',
    'permission:gallery.manage'
])->group(function () {

    Route::post('/gallery', [
        GalleryController::class,
        'store'
    ])->name('gallery.store');

    Route::put('/gallery/{gallery}', [
        GalleryController::class,
        'update'
    ])->name('gallery.update');

    Route::delete('/gallery/{gallery}', [
        GalleryController::class,
        'destroy'
    ])->name('gallery.destroy');

});



/*
|--------------------------------------------------------------------------
| CLASS SCHEDULES
|--------------------------------------------------------------------------
|
| Admin + Receptionist = manage
| Instructor = view
|
*/

Route::middleware([
    'auth',
    'permission:class_schedules.view'
])->group(function () {

    Route::get('/class-schedules', [
        ClassScheduleController::class,
        'index'
    ])->name('class_schedules.index');

    Route::get('/class_schedules_list', [
        ClassScheduleController::class,
        'class_schedules_list'
    ])->name('class_schedules_list');

});


Route::middleware([
    'auth',
    'permission:class_schedules.manage'
])->group(function () {

    Route::post('/class-schedules', [
        ClassScheduleController::class,
        'store'
    ])->name('class_schedules.store');

    Route::put('/class-schedules/{classSchedule}', [
        ClassScheduleController::class,
        'update'
    ])->name('class_schedules.update');

    Route::delete('/class-schedules/{classSchedule}', [
        ClassScheduleController::class,
        'destroy'
    ])->name('class_schedules.destroy');

    Route::put('/class/cancel/{classSchedule}', [
        ClassScheduleController::class,
        'cancel'
    ])->name('class_schedules.cancel');

    Route::put('/class-schedules/{classSchedule}/instructors', [
        ClassScheduleController::class,
        'updateInstructors'
    ])->name('schedules.update-instructors');

});



/*
|--------------------------------------------------------------------------
| INSTRUCTOR MANAGEMENT
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/
// Instructor Resource Routes
Route::resource('instructors', InstructorController::class)->except(['create', 'show', 'edit']);
Route::middleware([
    'auth',
    'permission:instructors.view'
])->group(function () {

    Route::get('/instructors', [
        InstructorController::class,
        'index'
    ])->name('instructors.index');

});


Route::middleware([
    'auth',
    'permission:instructors.manage'
])->group(function () {

    Route::get('/instructors/create', [
        InstructorController::class,
        'create'
    ])->name('instructors.create');

    Route::post('/instructors', [
        InstructorController::class,
        'store'
    ])->name('instructors.store');

    Route::get('/instructors/{instructor}/edit', [
        InstructorController::class,
        'edit'
    ])->name('instructors.edit');

    Route::put('/instructors/{instructor}', [
        InstructorController::class,
        'update'
    ])->name('instructors.update');

    Route::delete('/instructors/{instructor}', [
        InstructorController::class,
        'destroy'
    ])->name('instructors.destroy');

});



/*
|--------------------------------------------------------------------------
| INSTRUCTOR EARNINGS
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:instructors.manage'
])->group(function () {

    Route::get('/get/instructor/earnings', [
        EarningsController::class,
        'getInstructorEarnings'
    ])->name('instructor.earnings');

    Route::put('/instructors/{instructor}/earnings', [
        EarningsController::class,
        'updateInstructorEarnings'
    ])->name('instructors.earnings.update');

});



/*
|--------------------------------------------------------------------------
| ATTENDANCE
|--------------------------------------------------------------------------
|
| Attendance viewing
|
*/

Route::middleware([
    'auth',
    'permission:attendance.view'
])->group(function () {

    Route::get('/attendances', [
        AttendanceController::class,
        'index'
    ])->name('attendances.index');

    Route::get('/attendance/data', [
        AttendanceController::class,
        'attendanceData'
    ])->name('attendances.data');

    Route::get('/attendance/day-details', [
        AttendanceController::class,
        'attendanceDayDetails'
    ])->name('attendances.day-details');

});



/*
|--------------------------------------------------------------------------
| ATTENDANCE RECORD
|--------------------------------------------------------------------------
|
| Admin + Instructor
|
*/

Route::middleware([
    'auth',
    'permission:attendance.record'
])->group(function () {

    Route::get('/record', [
        AttendanceController::class,
        'record'
    ])->name('attendances.record');

    Route::post('/attendances/inTime', [
        AttendanceController::class,
        'inTime'
    ])->name('attendances.inTime');

    Route::post('/attendances/client/inTime', [
        AttendanceController::class,
        'ClientinTime'
    ])->name('attendances.ClientinTime');

});


/*
|--------------------------------------------------------------------------
| ATTENDANCE ADMIN APPROVAL
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'permission:attendance.manage'
])->group(function () {

    Route::put('/admin/approve/{instructor}', [
        AttendanceController::class,
        'adminApprove'
    ])->name('attendances.adminApprove');

});



/*
|--------------------------------------------------------------------------
| PAYMENTS
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:payments.view'
])->group(function () {

    Route::get('/payments', [
        PaymentController::class,
        'index'
    ])->name('payments.index');

});


Route::middleware([
    'auth',
    'permission:payments.manage'
])->group(function () {

    Route::post('/payments', [
        PaymentController::class,
        'store'
    ])->name('payments.store');

    Route::put('/payments/{payment}', [
        PaymentController::class,
        'update'
    ])->name('payments.update');

    Route::delete('/payments/{payment}', [
        PaymentController::class,
        'destroy'
    ])->name('payments.destroy');

});



/*
|--------------------------------------------------------------------------
| TRANSACTIONS
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:transactions.view'
])->group(function () {

    Route::get('/manage/purchases', [
        PaymentController::class,
        'managePurchases'
    ])->name('purchases.manage.page');

});


Route::middleware([
    'auth',
    'permission:transactions.manage'
])->group(function () {

    Route::patch('/transactions/{id}/status', [
        PaymentController::class,
        'updateStatus'
    ])->name('transactions.update-status');

    Route::delete('/transactions/{id}', [
        PaymentController::class,
        'destroyPurchase'
    ])->name('transactions.destroy');

});



/*
|--------------------------------------------------------------------------
| CATEGORIES
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/

Route::middleware([
    'auth',
    'permission:categories.view'
])->group(function () {

    Route::get('/categories', [
        CategoryController::class,
        'index'
    ])->name('categories.index');

});


Route::middleware([
    'auth',
    'permission:categories.manage'
])->group(function () {

    Route::post('/categories', [
        CategoryController::class,
        'store'
    ])->name('categories.store');

    Route::put('/categories/{category}', [
        CategoryController::class,
        'update'
    ])->name('categories.update');

    Route::delete('/categories/{category}', [
        CategoryController::class,
        'destroy'
    ])->name('categories.destroy');

});



/*
|--------------------------------------------------------------------------
| PACKAGES
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/

Route::middleware([
    'auth',
    'permission:packages.view'
])->group(function () {

    Route::get('/package', [
        PackageController::class,
        'index'
    ])->name('packages.index');

    Route::get('/get-classes-by-category/{categoryId}', [
        PackageController::class,
        'getClassesByCategory'
    ])->name('packages.get-classes-by-category');

});


Route::middleware([
    'auth',
    'permission:packages.manage'
])->group(function () {

    Route::post('/package', [
        PackageController::class,
        'store'
    ])->name('packages.store');

    Route::put('/package/{package}', [
        PackageController::class,
        'update'
    ])->name('packages.update');

    Route::delete('/package/{package}', [
        PackageController::class,
        'destroy'
    ])->name('packages.destroy');

});



/*
|--------------------------------------------------------------------------
| PACKAGE DISCOUNTS
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/

Route::middleware([
    'auth',
    'permission:packages.manage'
])->group(function () {

    Route::get('/give/discount/{id}', [
        UserPackageDiscountController::class,
        'create'
    ])->name('packages.give-discount');

    Route::post('/give/discount', [
        UserPackageDiscountController::class,
        'store'
    ])->name('discount.store');

    Route::delete('/user/{userId}/package/{packageId}/remove', [
        UserPackageDiscountController::class,
        'destroy'
    ])->name('discount.remove');

    Route::get('/user/{userId}/package/index', [
        UserPackageDiscountController::class,
        'discountIndex'
    ])->name('discount.index');

    Route::put('/user/{userId}/package/{packageId}/update', [
        UserPackageDiscountController::class,
        'update'
    ])->name('discount.update');

});



/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
|
| Admin + Receptionist
|
*/

Route::middleware([
    'auth',
    'permission:users.view'
])->group(function () {

    Route::get('/users/with-packages', [
        UserController::class,
        'usersWithPackages'
    ])->name('user.with_packages');

    Route::get('/user/{id}/package/index', [
        UserController::class,
        'userPackageDetails'
    ])->name('user.package.details');

});


Route::middleware([
    'auth',
    'permission:users.manage'
])->group(function () {

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

        });


    Route::put('/user-register/{id}', [
        UserController::class,
        'role_update'
    ])->name('user_register.role_update');


    Route::post('/user-register/reset-password', [
        UserController::class,
        'resetPassword'
    ])->name('user_register.reset_password');

});



/*
|--------------------------------------------------------------------------
| BOOKINGS
|--------------------------------------------------------------------------
|
| Admin + Receptionist + Instructor
|
*/

Route::middleware([
    'auth',
    'permission:bookings.view'
])->group(function () {

    Route::get('/bookings', [
        BookingController::class,
        'getBookings'
    ])->name('bookings.index');

    Route::get('/waitlist', [
        BookingController::class,
        'getWaitList'
    ])->name('waitlist.index');

});


Route::middleware([
    'auth',
    'permission:bookings.manage'
])->group(function () {

    Route::patch('/bookings/status/{id}', [
        BookingController::class,
        'updateStatus'
    ])->name('bookings.update-status');

    Route::get('/cancel/booking/{id}', [
        BookingController::class,
        'cancelBooking'
    ])->name('bookings.cancel-booking');

    Route::post('/admin/booking/store', [
        BookingController::class,
        'adminBookClass'
    ])->name('admin.booking.store');

});



/*
|--------------------------------------------------------------------------
| REPORTS
|--------------------------------------------------------------------------
|
| Admin only
|
*/

Route::middleware([
    'auth',
    'permission:customer_reports.view'
])->group(function () {

    Route::get('/get/customer/report', [
        ReportController::class,
        'getCustomerReport'
    ])->name('customer.report');

    Route::get('/get/customer/report/{id}', [
        ReportController::class,
        'getCustomerPackagesAjax'
    ])->name('customer.packages.report');

});


Route::middleware([
    'auth',
    'permission:instructor_reports.view'
])->group(function () {

    Route::get('/get/instructor/report', [
        ReportController::class,
        'getInstructorReport'
    ])->name('instructor.report');

    Route::get('/get/instructor/report/{id}', [
        ReportController::class,
        'getInstructorPackagesAjax'
    ])->name('instructor.packages.report');

});


Route::middleware([
    'auth',
    'permission:reports.view'
])->group(function () {

    Route::get('/get/top/packages', [
        ReportController::class,
        'getTopPackages'
    ])->name('top.packages');

    Route::get('/get/monthly/sales', [
        ReportController::class,
        'getMonthlySales'
    ])->name('montly.sales');

});



/*
|--------------------------------------------------------------------------
| INSTRUCTOR REPORT
|--------------------------------------------------------------------------
|
| Instructor can record their report.
|
*/

Route::middleware([
    'auth',
    'permission:instructor_reports.record'
])->group(function () {

    Route::get('/get/instructor/it/report', [
        ReportController::class,
        'getInstructorReports'
    ])->name('instructor.it.reports');

});



/*
|--------------------------------------------------------------------------
| REQUIRE AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';