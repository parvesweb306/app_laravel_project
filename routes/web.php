<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Parent Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified'
])->group(function () {

    /*
     | Dashboard
     */
    Route::get(
        '/dashboard',
        [
            DeviceController::class,
            'index'
        ]
    )->name('dashboard');

    /*
     | Dashboard Live Status
     */
    Route::get(
        '/dashboard/status',
        [
            DeviceController::class,
            'dashboardStatus'
        ]
    )->name('dashboard.status');

    /*
     | Create Pairing
     */
    Route::post(
        '/devices/create-pairing',
        [
            DeviceController::class,
            'createPairing'
        ]
    )->name('devices.create-pairing');

    /*
     | LOCK
     */
    Route::post(
        '/devices/{device}/lock',
        [
            DeviceController::class,
            'lock'
        ]
    )->name('devices.lock');

    /*
     | UNLOCK
     */
    Route::post(
        '/devices/{device}/unlock',
        [
            DeviceController::class,
            'unlock'
        ]
    )->name('devices.unlock');

    /*
     | Set Unlock PIN
     */
    Route::post(
        '/devices/{device}/set-unlock-pin',
        [
            DeviceController::class,
            'setUnlockPin'
        ]
    )->name('devices.set-unlock-pin');
});

/*
|--------------------------------------------------------------------------
| PUBLIC CHILD DOWNLOAD PAGE
|--------------------------------------------------------------------------
*/

Route::get(
    '/dl/{code}',
    [
        DeviceController::class,
        'downloadPage',
    ]
)->name('devices.download-page');

/*
|--------------------------------------------------------------------------
| PUBLIC APK DOWNLOAD
|--------------------------------------------------------------------------
*/

Route::get(
    '/dl/{code}/apk',
    [
        DeviceController::class,
        'downloadApk',
    ]
)->name('devices.download-apk');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit'
        ]
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update'
        ]
    )->name('profile.update');

    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy'
        ]
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';