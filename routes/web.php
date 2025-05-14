<?php

use App\Http\Controllers\admin\AddNasabahController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\HasilWPController;
use App\Http\Controllers\admin\KriteriaController;
use App\Http\Controllers\admin\PenilaianWPController;
use App\Http\Controllers\user\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role->name === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('home');
        }
    }
    return redirect()->route('login');
})->name('home');

Auth::routes(['middleware' => ['redirectIfAuthenticated']]);


Route::middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::resource('nasabah', AddNasabahController::class);
    Route::resource('kriteria', KriteriaController::class);
    Route::resource('penilaian', PenilaianWPController::class);
    Route::resource('hasil-wp', HasilWPController::class);
});
