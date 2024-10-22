<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\DeliveryController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\RestrauntController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//      // Prevent delivery boys from accessing customer dashboard
//      if (Auth::user()->role === 'delivery_boy') {
//         abort(403, 'Unauthorized access.');
//     }
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Customer Routes
Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Admin Routes
// Route::middleware('guest')->group(function () {
//     Route::get('admin/login', [AdminController::class, 'adminLogin'])
//     ->name('admin.login');

//     Route::post('admin/login', [AdminController::class, 'adminLogin'])
//     ->name('admin.login');

// });

// Route::prefix('admin')->name('admin.')->namespace('App\Http\Controllers\Backend')->group(function(){
//     // Route::middleware(['guest'])->group(function () {
//         Route::match(['get' , 'post'] ,'login' ,  'AdminController@login')->name('login')->middleware('guest');

//         // Route::middleware(['admin'])->group(function () {
//         //     Route::get('dashboard' , 'AdminController@dashboard')->name('dashboard');
//         // });

// });
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
//     Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
//     // other admin routes...
// });


Route::prefix('admin')->name('admin.')->group(function() {
    // Guest routes
    Route::middleware('guest')->group(function() {
        Route::match(['get', 'post'], '/login', [AdminController::class, 'login'])
            ->name('login');
    });
    
    // Admin protected routes using default guard
    Route::middleware(['auth', 'admin'])->group(function() {
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        // Restaurants

        Route::resource('restaurants', RestrauntController::class);
    });
});


// Delivery Routes
Route::middleware(['auth', 'delivery'])->group(function () {
    Route::get('/delivery/dashboard', [DeliveryController::class, 'dashboard'])->name('delivery.dashboard');
    Route::get('/delivery/profile', [DeliveryController::class, 'profile'])->name('delivery.profile');
    // other delivery routes...
});


// // Customer Routes
// Route::middleware(['auth', 'customer'])->group(function () {
//     Route::get('/restaurants', [CustomerController::class, 'listRestaurants'])->name('customer.restaurants');
//     // other customer routes...
// });

require __DIR__.'/auth.php';
