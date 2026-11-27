<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return auth()->check() ? redirect(auth()->user()->role==='admin'?'/admin/dashboard':'/nasabah/dashboard') : redirect('/login');
});
Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'showLogin'])->name('login');
    Route::post('/login',[AuthController::class,'login'])->middleware('throttle:10,1');
    Route::get('/register',[AuthController::class,'showRegister'])->name('register');
    Route::post('/register',[AuthController::class,'register']);
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
    Route::get('/nasabah',[AdminController::class,'users'])->name('users');
    Route::post('/nasabah',[AdminController::class,'storeUser'])->name('users.store');
    Route::put('/nasabah/{user}',[AdminController::class,'updateUser'])->name('users.update');
    Route::delete('/nasabah/{user}',[AdminController::class,'deleteUser'])->name('users.delete');
    Route::get('/sampah',[AdminController::class,'wastes'])->name('wastes');
    Route::post('/sampah',[AdminController::class,'storeWaste'])->name('wastes.store');
    Route::put('/sampah/{waste}',[AdminController::class,'updateWaste'])->name('wastes.update');
    Route::delete('/sampah/{waste}',[AdminController::class,'deleteWaste'])->name('wastes.delete');
    Route::get('/setoran',[AdminController::class,'deposits'])->name('deposits');
    Route::post('/setoran',[AdminController::class,'storeDeposit'])->name('deposits.store');
    Route::post('/setoran/{deposit}/cancel',[AdminController::class,'cancelDeposit'])->name('deposits.cancel');
    Route::get('/penarikan',[AdminController::class,'withdrawals'])->name('withdrawals');
    Route::post('/penarikan/{request}/approve',[AdminController::class,'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/penarikan/{request}/reject',[AdminController::class,'rejectWithdrawal'])->name('withdrawals.reject');
    Route::get('/laporan',[AdminController::class,'reports'])->name('reports');
});
Route::middleware(['auth','role:nasabah'])->prefix('nasabah')->name('nasabah.')->group(function(){
    Route::get('/dashboard',[NasabahController::class,'dashboard'])->name('dashboard');
    Route::get('/harga',[NasabahController::class,'wastes'])->name('wastes');
    Route::get('/setoran',[NasabahController::class,'deposits'])->name('deposits');
    Route::get('/penarikan',[NasabahController::class,'withdrawal'])->name('withdrawal');
    Route::post('/penarikan',[NasabahController::class,'storeWithdrawal'])->name('withdrawal.store');
    Route::get('/profil',[ProfileController::class,'show'])->name('profile');
    Route::put('/profil',[ProfileController::class,'update'])->name('profile.update');
});
