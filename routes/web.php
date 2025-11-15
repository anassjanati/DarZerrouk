<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\MagasinageController;
use App\Http\Controllers\Admin\BonDeCommandeController;

Route::get('/', function () {
    return view('welcome');
});

// -------------------- ADMIN & MANAGER ROUTES --------------------------
Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    // Magasinage (Stock Management)
    Route::get('magasinage', [MagasinageController::class, 'index'])->name('magasinage.index');
    Route::post('stocks', [MagasinageController::class, 'store'])->name('stocks.store');
    Route::post('stock/transfer', [MagasinageController::class, 'transfer'])->name('stock.transfer');

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Book Management
    Route::get('books/manage', [BookManagementController::class, 'index'])->name('books.manage');
    Route::get('books/{book}/details', [BookManagementController::class, 'show'])->name('books.details');
    Route::get('books/stock-alerts', [BookManagementController::class, 'stockAlerts'])->name('books.stock-alerts');
    Route::patch('books/{book}/zone', [BookManagementController::class, 'updateZone'])->name('books.updateZone');
    Route::get('books/import', [BookManagementController::class, 'importForm'])->name('books.import');
    Route::post('books/import', [BookManagementController::class, 'importStore'])->name('books.import.store');

    // Archive/Unarchive actions and listing
    Route::get('books/archive', [BookController::class, 'archiveList'])->name('books.archiveList');
    Route::post('books/{book}/archive', [BookController::class, 'archive'])->name('books.archive');
    Route::post('books/{book}/unarchive', [BookController::class, 'unarchive'])->name('books.unarchive');

    // Book CRUD
    Route::resource('books', BookController::class);

    // Other Book Management Resources
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('authors', \App\Http\Controllers\Admin\AuthorController::class);
    Route::resource('translators', \App\Http\Controllers\Admin\TranslatorController::class);
    Route::resource('editors', \App\Http\Controllers\Admin\EditorController::class);
    Route::resource('publishers', \App\Http\Controllers\Admin\PublisherController::class);

    // User Management
    Route::resource('users', UserController::class);
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

    // Zones
    Route::get('zones/overview', [ZoneController::class, 'overview'])->name('zones.overview');
    Route::get('zones/{zone}/books', [ZoneController::class, 'booksInZone'])->name('zones.books');
    Route::resource('zones', ZoneController::class);

    // "Bon de Commande" routes - use the SAME parameter everywhere: {bon_de_commande}
    Route::resource('bon_de_commande', BonDeCommandeController::class);

    // Custom print route: `{bon_de_commande}` not `{bon}`!
    Route::get('bon_de_commande/{bon_de_commande}/print', [BonDeCommandeController::class, 'print'])->name('bon_de_commande.print');
    Route::get('bon_de_commande/history', [BonDeCommandeController::class, 'history'])->name('bon_de_commande.history');
});

// -------------------- GENERAL LOGGED-IN USER ROUTES --------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isCashier()) return redirect()->route('pos.index');
        if (auth()->user()->isAdmin() || auth()->user()->isManager()) return redirect()->route('admin.dashboard');
        return view('dashboard');
    })->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS (Cashier/Manager)
    Route::middleware('role:cashier,manager')->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [POSController::class, 'searchBooks'])->name('pos.search');
        Route::get('/pos/book/{code}', [POSController::class, 'getBook'])->name('pos.getBook');
        Route::post('/pos/sale', [POSController::class, 'processSale'])->name('pos.sale');
    });
});

require __DIR__.'/auth.php';
