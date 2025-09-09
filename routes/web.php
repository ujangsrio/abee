<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\HistoriLayananController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\MultiAuthController;

use App\Http\Controllers\Customer\CustomerLoginController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\DetailAkunController;
use App\Http\Controllers\Customer\CustomerMembershipController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\CustomerLoginAuthController;
use App\Http\Middleware\IsCustomer;


// Landing Page
Route::get('/', function () {
    return view('landingpage');
});


// ===================== ADMIN AREA =====================
// 1. Show satu form login untuk ADMIN & CUSTOMER
Route::get('/login', [MultiAuthController::class, 'showLoginForm'])
    ->name('login');

// 2. Proses login: coba guard admin dulu, lalu customer
Route::post('/login', [MultiAuthController::class, 'login'])
    ->name('login.submit');

// Register khusus customer
Route::get('/customer/register', [MultiAuthController::class, 'showCustomerRegisterForm'])->name('customer.register');
Route::post('/customer/register', [MultiAuthController::class, 'customerRegister'])->name('customer.register.submit');

Route::post('/customer/logout', [MultiAuthController::class, 'logout'])->name('customer.logout');


// 3. Proses logout: logout dari guard mana pun yang sedang aktif
Route::post('/logout', [MultiAuthController::class, 'logout'])
    ->name('logout');

// Admin Area Routes, dengan middleware auth:admin dan prefix 'admin', name 'admin.'
Route::middleware(['auth:admin', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])
            ->name('dashboard');

        // Resource routes admin
        Route::resource('layanan', LayananController::class);
        Route::resource('promo', PromoController::class);
        Route::resource('membership', MembershipController::class);

        //Slot
        Route::post('/admin/slots', [SlotController::class, 'store'])->name('slots.store');


        // Membership
        Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
        Route::post('/membership', [MembershipController::class, 'store'])->name('membership.store');
        Route::get('/membership/{id}/edit', [MembershipController::class, 'edit'])->name('membership.edit');
        Route::put('/membership/{id}', [MembershipController::class, 'update'])->name('membership.update');

        Route::get('/membership/create-pelanggan', [MembershipController::class, 'createPelanggan'])->name('membership.createPelanggan');
        Route::post('/membership/store-pelanggan', [MembershipController::class, 'storePelanggan'])->name('membership.storePelanggan');

        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');

        Route::get('/histori-layanan', [HistoriLayananController::class, 'index'])->name('histori.index');

        Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
        Route::post('/reservasi/{id}/terima', [ReservasiController::class, 'terima'])->name('reservasi.terima');
        Route::post('/reservasi/{id}/tolak', [ReservasiController::class, 'tolak'])->name('reservasi.tolak');
        Route::patch('/reservasi/{id}/update-status', [ReservasiController::class, 'updateStatus'])->name('reservasi.updateStatus');
        Route::post('/reservasi/{id}/confirm-dp', [ReservasiController::class, 'confirmDp'])->name('reservasi.confirmDp');
        Route::post('/reservasi/{id}/reject-dp', [ReservasiController::class, 'rejectDp'])->name('reservasi.rejectDp');

        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

        // Profile admin
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });



// ===================== CUSTOMER AREA =====================

// Halaman Dashboard & Menu Customer setelah login
Route::middleware(['web', 'auth:customer', IsCustomer::class])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        // Route::get('/booking', function () {
        //     return 'Halaman booking belum dibuat';
        // })->name('booking');
        Route::get('/dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/reservasiaktif', [BookingController::class, 'reservasiaktif'])->name('reservasiaktif');
        Route::get('/layanan', [CustomerDashboardController::class, 'layanan'])->name('layanan');
        Route::get('/kontak', [CustomerDashboardController::class, 'kontak'])->name('kontak');
        Route::get('/cari', [CustomerDashboardController::class, 'cari'])->name('cari');
        Route::get('/profil', [CustomerProfileController::class, 'index'])->name('profil');
        Route::put('/profil', [CustomerProfileController::class, 'update'])->name('profil.update');
        Route::patch('/profil/{id}', [CustomerProfileController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [CustomerProfileController::class, 'updatePassword'])->name('profil.updatePassword');
        Route::get('/logout', [CustomerDashboardController::class, 'logout'])->name('logout');
        Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/available-times', [BookingController::class, 'availableTimes'])->name('booking.availableTimes');
        Route::get('/calculate-cost', [BookingController::class, 'calculateTotalCost'])->name('booking.calculateCost');
        Route::get('/booking/jam-tersedia-by-tanggal', [BookingController::class, 'availableTimesByDate'])->name('customer.booking.timesByDate');
        Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
        Route::delete('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        Route::get('/akun', [DetailAkunController::class, 'index'])->name('akun.index');
        Route::get('/akun/membership', [CustomerMembershipController::class, 'create'])->name('akun.membership_form');
        Route::post('/akun/membership', [CustomerMembershipController::class, 'store'])->name('akun.membership_register');
        Route::get('/history', [BookingController::class, 'history'])->name('history.index');
    });


// require __DIR__.'/auth.php';
