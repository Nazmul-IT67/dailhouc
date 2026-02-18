<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\DynamicPageController;
use App\Http\Controllers\API\FirebaseTokenController;
use App\Http\Controllers\API\Location\LocationController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SocialMediaController;
use App\Http\Controllers\API\SystemSettingController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\Vehicle\GetVehicleController;
use App\Http\Controllers\API\Vehicle\StoreVehicleController;
use App\Http\Controllers\API\Vehicle\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("register", [AuthController::class, 'register']);
Route::post("login", [AuthController::class, 'login']);

Route::controller(RegisterController::class)->prefix('users/register')->group(function () {
    // User Register
    Route::post('/', 'userRegister');
    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');
    //email exists check
    Route::post('/email-exists', 'emailExists');
});

Route::controller(LoginController::class)->prefix('users/login')->group(function () {
    // User Login
    Route::post('/', 'userLogin');

    // Verify Email
    Route::post('/email-verify', 'emailVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    //Reset Password
    Route::post('/reset-password', 'resetPassword');
});

Route::prefix('location')->controller(LocationController::class)->group(function () {
    Route::get('/countries', 'getCountry');
    Route::get('/cities', 'getCity');
});

Route::prefix('/vehicle/search')->controller(SearchController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/save-search', 'saveSearch');
    Route::delete('/delete-search', 'deleteSearch');

    Route::get('/latest-search', 'latestPopular');
    Route::get('/last-filters', 'getFilteredSearch');
});

Route::prefix('vehicle')->controller(VehicleController::class)->group(function () {
    Route::get('/categories', 'getCategory');
    Route::get('/brands', 'getBrandByCategory');
    Route::get('/models', 'getModelsByBrand');
    Route::get('/submodels', 'getSubModelsByModel');
    Route::get('/models-submodel', 'getModelsWithSubModelsByBrand');
    Route::get('/vehicle-conditions', 'getVehicleConditions');
    Route::get('/vehicle-body-color', 'getBodyColors');
    Route::get('/vehicle-upholstery', 'getUpholsteries');
    Route::get('/vehicle-interior-colors', 'getInteriorColors');
    Route::get('/previour-owner', 'getPreviousOwners');
    Route::get('/number-of-doors', 'getNumberOfDoors');
    Route::get('/number-of-seats', 'getNumberOfSeats');
    Route::get('/bed-count', 'getBedCounts');
    Route::get('/bed-types', 'getBedTypes');
    Route::get('/driver-types', 'getDriverTypes');
    Route::get('/transmission', 'getTransmission');
    Route::get('/axle-count', 'getAxleCount');
    Route::get('/no-of-gears', 'getNumOfGears');
    Route::get('/cylinders', 'getNumOfCylinders');
    Route::get('/emission-classes', 'getEmissionClasses');
    Route::get('/fuel-types', 'getFuelTypes');
    Route::get('/equipment', 'getEquipment');
    Route::get('/body-types', 'getBodyTypes');
    Route::get('/power', 'getPower');
    Route::get('/equipment-line', 'getEquipmentLine');
    Route::get('/seller-types', 'getSellerTypes');
    Route::post('/favorite', 'toggleFavorite');
    Route::get('/favorites', 'getFavoriteVehicles');
    Route::get('/model-year', 'getModelYears');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(SystemSettingController::class)->group(function () {
        Route::get('/site-settings', 'index');
    });

    Route::controller(SocialMediaController::class)->group(function () {
        Route::get('/social-links', 'index');
    });

    Route::controller(BlogController::class)->group(function () {
        Route::get('/blogs', 'index');
    });

    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('/', 'userDetails');
        Route::post('/update', 'updateUser');
        Route::post('/update-password', 'updatePassword');
        Route::delete('/delete-account', 'deleteAccount');
        Route::post('/logout', 'logoutUser');
    });

    Route::prefix('vehicle')->controller(StoreVehicleController::class)->group(function () {
        Route::post('/store', 'store');
    });

    Route::prefix('vehicle')->controller(GetVehicleController::class)->group(function () {
        Route::get('/get-all-vehicle', 'getAllVehicles');
        Route::get('/get-users-vehicle', 'getUsersVehicle');
        // Route::get('/get-vehicle-by-userId', 'getVehiclesByUserId');
        Route::get('/get-pending-and-all', 'getPendingAndAllVehicle');
        Route::get('/vehicle-details', 'vehicleDetails');
        Route::get('/featured', 'featured');

    });

    Route::prefix('vehicle')->controller(VehicleController::class)->group(function () {
        Route::post('/favorite', 'toggleFavorite');
        Route::get('/favorites', 'getFavoriteVehicles');
    });

    Route::prefix('firebase')->controller(FirebaseTokenController::class)->group(function () {
        Route::post('/update-token', 'updateFirebaseToken');   // save/update device token
        Route::post('/delete-token', 'deleteFirebaseToken');
        Route::post('/test-notification', 'testNotification');
    });

    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index');            // all notifications
        Route::get('/unread', 'unread');     // unread notifications
        Route::post('/{id}/read', 'markAsRead'); // mark one as read
        Route::post('/read-all', 'markAllAsRead'); // mark all as read
        Route::delete('/delete', 'delete');
    });

    Route::prefix('/vehicle/search')->controller(SearchController::class)->group(function () {
        Route::get('/last-filters', 'getFilteredSearch');
    });

});

Route::prefix('vehicle')->controller(GetVehicleController::class)->group(function () {
    Route::get('/get-vehicle-by-userId', 'getVehiclesByUserId');
});

Route::prefix('pages')->group(function () {
    Route::get('/', [DynamicPageController::class, 'index']);   // GET /api/pages
    Route::get('/{slug}', [DynamicPageController::class, 'show']); // GET /api/pages/{slug}
});