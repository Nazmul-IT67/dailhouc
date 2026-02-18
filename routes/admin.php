<?php

use App\Http\Controllers\Admin\BedCountController;
use App\Http\Controllers\Admin\BedTypeController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BodyColorController;
use App\Http\Controllers\Admin\BodyTypeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CurrencyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\DynamicPageController;
use App\Http\Controllers\Admin\Engine_And_Environment\AxleCountController;
use App\Http\Controllers\Admin\Engine_And_Environment\CylinderController;
use App\Http\Controllers\Admin\Engine_And_Environment\DriverTypeController;
use App\Http\Controllers\Admin\Engine_And_Environment\EmissionClassController;
use App\Http\Controllers\Admin\Engine_And_Environment\NumOfGearController;
use App\Http\Controllers\Admin\Engine_And_Environment\TransmissionController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\EquipmentLineController;
use App\Http\Controllers\Admin\FuelController;
use App\Http\Controllers\Admin\InteriorColorController;
use App\Http\Controllers\Admin\PreviousOwnerController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\SubModelController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UpholsteryController;
use App\Http\Controllers\Admin\VehicleConditionController;
use App\Http\Controllers\Admin\NumberOfDoorController;
use App\Http\Controllers\Admin\NumberOfSeatsController;
use App\Http\Controllers\Admin\PowerController;
use App\Http\Controllers\Admin\SellerTypeController;
use App\Http\Controllers\Admin\ModelYearController;
use App\Http\Controllers\Admin\GetVehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\GetVehicleListController;
use App\Models\CarModel;
use App\Models\NumberOfSeat;
use App\Models\Upholstery;

Route::get('/dashboard', [DashBoardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::controller(SystemSettingController::class)->group(function () {
    Route::get('/system-setting', 'index')->name('system.index');
    Route::post('/system-setting', 'update')->name('system.update');
});
Route::controller(ProfileController::class)->group(function () {
    Route::post('/update-profile-picture', 'UpdateProfilePicture')->name('update.profile.picture');
    Route::post('/update-profile-password', 'UpdatePassword')->name('update.Password');

    //! Route for ProfileController
    Route::get('/profile', 'showProfile')->name('profile.setting');
    Route::post('/update-profile', 'UpdateProfile')->name('update.profile');
});
Route::controller(SocialMediaController::class)->group(function () {
    Route::get('/social-media', 'index')->name('social.index');
    Route::post('/social-media', 'update')->name('social.update');
    Route::delete('/social-media/{id}', 'destroy')->name('social.delete');
});
Route::resource('blogs', BlogController::class)->names('blogs');
Route::post('blogs/{id}/status', [BlogController::class, 'updateStatus'])
    ->name('blogs.updateStatus');

Route::controller(DynamicPageController::class)->group(function () {
    Route::get('/dynamic-page', 'index')->name('dynamic_page.index');
    Route::get('/dynamic-page/create', 'create')->name('dynamic_page.create');
    Route::post('/dynamic-page/store', 'store')->name('dynamic_page.store');
    Route::get('/dynamic-page/edit/{id}', 'edit')->name('dynamic_page.edit');
    Route::post('/dynamic-page/update/{id}', 'update')->name('dynamic_page.update');
    Route::get('/dynamic-page/status/{id}', 'status')->name('dynamic_page.status');
    Route::delete('/dynamic-page/destroy/{id}', 'destroy')->name('dynamic_page.destroy');
});

Route::controller(CurrencyController::class)->group(function () {
    Route::get('/currencies', 'index')->name('currencies.index');
    Route::get('/currencies/create', 'create')->name('currencies.create');
    Route::post('/currencies/store', 'store')->name('currencies.store');
    Route::get('/currencies/edit/{id}', 'edit')->name('currencies.edit');
    Route::post('/currencies/update/{id}', 'update')->name('currencies.update');
    Route::get('/currencies/status/{id}', 'status')->name('currencies.status');
    Route::delete('/currencies/destroy/{id}', 'destroy')->name('currencies.destroy');
});
Route::controller(CountryController::class)->group(function () {
    Route::get('/countries', 'index')->name('countries.index');
    Route::get('/countries/create', 'create')->name('countries.create');
    Route::post('/countries/store', 'store')->name('countries.store');
    Route::get('/countries/edit/{id}', 'edit')->name('countries.edit');
    Route::post('/countries/update/{id}', 'update')->name('countries.update');
    Route::get('/countries/status/{id}', 'status')->name('countries.status');
    Route::delete('/countries/destroy/{id}', 'destroy')->name('countries.destroy');
});
Route::controller(CityController::class)->group(function () {
    Route::get('/cities', 'index')->name('cities.index');
    Route::get('/cities/create', 'create')->name('cities.create');
    Route::post('/cities/store', 'store')->name('cities.store');
    Route::get('/cities/edit/{id}', 'edit')->name('cities.edit');
    Route::post('/cities/update/{id}', 'update')->name('cities.update');
    Route::get('/cities/status/{id}', 'status')->name('cities.status');
    Route::delete('/cities/destroy/{id}', 'destroy')->name('cities.destroy');
});
Route::prefix('categories')->name('categories.')->controller(CategoryController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('brands')->name('brands.')->controller(BrandController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('car_models')->name('car_models.')->controller(CarModelController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('sub_models')->name('sub_models.')->controller(SubModelController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('vehicle-condition')->name('vehicle_conditions.')->controller(VehicleConditionController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('body-colors')->name('body_colors.')->controller(BodyColorController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('upholsteries')->name('upholsteries.')->controller(UpholsteryController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('interior_colors')->name('interior_colors.')->controller(InteriorColorController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('previous_owners')->name('previous_owners.')->controller(PreviousOwnerController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('number_of_doors')->name('number_of_doors.')->controller(NumberOfDoorController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('number_of_seats')->name('number_of_seats.')->controller(NumberOfSeatsController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('bed_counts')->name('bed_counts.')->controller(BedCountController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('bed_types')->name('bed_types.')->controller(BedTypeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('driver_types')->name('driver_types.')->controller(DriverTypeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('transmissions')->name('transmissions.')->controller(TransmissionController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('num_of_gears')->name('num_of_gears.')->controller(NumOfGearController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('cylinders')->name('cylinders.')->controller(CylinderController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('axle-counts')->name('axle-counts.')->controller(AxleCountController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('emission_classes')->name('emission_classes.')->controller(EmissionClassController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('fuels')->name('fuels.')->controller(FuelController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('equipment')->name('equipment.')->controller(EquipmentController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('body_types')->name('body_types.')->controller(BodyTypeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('powers')->name('powers.')->controller(PowerController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('equipment_lines')->name('equipment_lines.')->controller(EquipmentLineController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('seller_types')->name('seller_types.')->controller(SellerTypeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('model_years')->name('model_years.')->controller(ModelYearController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
Route::prefix('vehicles')->name('vehicles.')->controller(GetVehicleListController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/vehicles/{id}', 'show')->name('show');
    Route::patch('/update-status/{id}', 'updateStatus')->name('updateStatus');
    Route::patch('/update-feature/{vehicle}', 'updateFeature');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
});
