<?php

use App\Http\Controllers\BankCreditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    // Redirect to /bank-credits if authenticated, otherwise show Login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('bank-credits');
    }
    return Inertia::render('Auth/Login');
});

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/bank-credits', [BankCreditController::class, 'index'])->name('bank-credits');
    Route::get('/bank-credits/search', [BankCreditController::class, 'find'])->name('bank-credits.find');
    Route::get('/bank-credits/create', [BankCreditController::class, 'create'])->name('bank-credits.create');
    Route::post('/bank-credits', [BankCreditController::class, 'store'])->name('bank-credits.store');

    Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
});

require __DIR__.'/auth.php';
