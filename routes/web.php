<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated routes
Route::middleware(['auth', 'check.active', 'auto.logout'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Force change password
    Route::get('/change-password', [AuthController::class, 'showForceChangePassword'])->name('password.force-change');
    Route::post('/change-password', [AuthController::class, 'forceChangePassword'])->name('password.force-update');

    // All authenticated routes require password changed
    Route::middleware('force.password.change')->group(function () {

        // Profile (all roles)
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

        // eBook browsing & reading (all authenticated roles)
        Route::get('/ebooks', [EbookController::class, 'index'])->name('ebooks.index');
        Route::get('/ebooks/{ebook}', [EbookController::class, 'show'])->name('ebooks.show');
        Route::get('/ebooks/{ebook}/read', [EbookController::class, 'read'])->name('ebooks.read');
        Route::get('/pdf/stream/{ebook}', [EbookController::class, 'streamPdf'])->name('pdf.stream');

        // ====== USER ROUTES ======
        Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
        });

        // ====== ADMIN ROUTES ======
        Route::middleware('role:admin,superadmin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

            // User management
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
            Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::get('/users/template', [UserController::class, 'template'])->name('users.template');
            Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

            // eBook management
            Route::get('/ebooks', [EbookController::class, 'adminIndex'])->name('ebooks.index');
            Route::get('/ebooks/create', [EbookController::class, 'create'])->name('ebooks.create');
            Route::post('/ebooks', [EbookController::class, 'store'])->name('ebooks.store');
            Route::get('/ebooks/{ebook}/edit', [EbookController::class, 'edit'])->name('ebooks.edit');
            Route::put('/ebooks/{ebook}', [EbookController::class, 'update'])->name('ebooks.update');
            Route::delete('/ebooks/{ebook}', [EbookController::class, 'destroy'])->name('ebooks.destroy');
            Route::patch('/ebooks/{ebook}/toggle', [EbookController::class, 'toggleStatus'])->name('ebooks.toggle');

            // Categories
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // ====== SUPERADMIN ROUTES ======
        Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');

            // Admin management
            Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
            Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
            Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
            Route::get('/admins/{admin}/edit', [AdminManagementController::class, 'edit'])->name('admins.edit');
            Route::put('/admins/{admin}', [AdminManagementController::class, 'update'])->name('admins.update');
            Route::patch('/admins/{admin}/toggle', [AdminManagementController::class, 'toggleStatus'])->name('admins.toggle');
            Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');

            // Activity logs
            Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
            Route::get('/logs/failed-logins', [ActivityLogController::class, 'failedLogins'])->name('logs.failed');
            Route::get('/logs/security', [ActivityLogController::class, 'security'])->name('logs.security');
        });
    });
});
