<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\BookController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('magasinage', [App\Http\Controllers\Admin\MagasinageController::class, 'index'])->name('magasinage.index');
    Route::post('stock/transfer', [App\Http\Controllers\Admin\MagasinageController::class, 'transfer'])->name('stock.transfer');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isCashier()) {
            return redirect()->route('pos.index');
        }
        if (auth()->user()->isAdmin() || auth()->user()->isManager()) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes (Admin & Manager only)
    Route::middleware('role:admin,manager')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('books/list', [BookManagementController::class, 'listAll'])->name('books.list');
        // User Management
        Route::resource('users', UserController::class);
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

        // Unified Books Management (MOVE BEFORE RESOURCE)
        Route::get('books/manage', [BookManagementController::class, 'index'])->name('books.manage');
        Route::get('books/{book}/details', [BookManagementController::class, 'show'])->name('books.details');
        Route::get('books/stock-alerts', [BookManagementController::class, 'stockAlerts'])->name('books.stock-alerts');
        Route::patch('books/{book}/zone', [BookManagementController::class, 'updateZone'])->name('books.updateZone');

        // Books Management Resources (KEEP BUT AFTER CUSTOM ROUTES)
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('authors', \App\Http\Controllers\Admin\AuthorController::class);
        Route::resource('translators', \App\Http\Controllers\Admin\TranslatorController::class);
        Route::resource('editors', \App\Http\Controllers\Admin\EditorController::class);
        Route::resource('publishers', \App\Http\Controllers\Admin\PublisherController::class);

        Route::resource('books', \App\Http\Controllers\Admin\BookController::class);

        Route::get('zones/overview', [ZoneController::class, 'overview'])->name('zones.overview');
        Route::get('zones/{zone}/books', [ZoneController::class, 'booksInZone'])->name('zones.books');

        Route::resource('zones', \App\Http\Controllers\Admin\ZoneController::class);
        Route::get('books/import', [BookManagementController::class, 'importForm'])->name('books.import');
        Route::post('books/import', [BookManagementController::class, 'importStore'])->name('books.import.store');
    });

    // POS Routes (Cashier & Manager only)
    Route::middleware('role:cashier,manager')->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [POSController::class, 'searchBooks'])->name('pos.search');
        Route::get('/pos/book/{code}', [POSController::class, 'getBook'])->name('pos.getBook');
        Route::post('/pos/sale', [POSController::class, 'processSale'])->name('pos.sale');
    });
});

require __DIR__.'/auth.php';
