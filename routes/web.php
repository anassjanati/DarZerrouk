<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\MagasinageController;
use App\Http\Controllers\Admin\BonDeCommandeController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\BookImportController;
use App\Http\Controllers\Admin\PosDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SupplierController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| ADMIN / BACKOFFICE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |-----------------------------------------
        | DASHBOARD (Admin & Superviseur)
        |-----------------------------------------
        */
        Route::middleware('role:admin,superviseur')
            ->get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |-----------------------------------------
        | POS DASHBOARD (Admin only)
        |-----------------------------------------
        */
        Route::middleware('role:admin')->group(function () {
            Route::get('pos-dashboard', [PosDashboardController::class, 'index'])
                ->name('pos.dashboard');
        });

        /*
        |-----------------------------------------
        | USERS (Admin Only)
        |-----------------------------------------
        */
        Route::middleware('role:admin')->group(function () {
            Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
            Route::resource('users', UserController::class);
            Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
            Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
        });

        /*
        |-----------------------------------------
        | CLIENTS (Admin / Superviseur / Manager)
        |-----------------------------------------
        */
        Route::middleware('role:admin,superviseur,manager')->group(function () {
            Route::resource('clients', ClientController::class)->names('clients');
        });

        /*
        |-----------------------------------------
        | SUPPLIERS (Admin / Superviseur / Manager)
        |-----------------------------------------
        */
        Route::middleware('role:admin,superviseur,manager')->group(function () {
            Route::resource('suppliers', SupplierController::class)
                ->only(['index', 'show'])
                ->names('suppliers');
        });

        /*
        |-----------------------------------------
        | BOOK IMPORT (Admin)
        |-----------------------------------------
        */
        Route::middleware('role:admin')
            ->post('books/import-csv', [BookImportController::class, 'handle'])
            ->name('books.import.csv');

        /*
        |-----------------------------------------
        | BON DE COMMANDE (ADMIN AREA)
        |-----------------------------------------
        */

        // Create (Manager & Superviseur via /admin/* if needed)
        Route::middleware('role:manager,superviseur')->group(function () {
            Route::get('bon_de_commande/create', [BonDeCommandeController::class, 'create'])->name('bon_de_commande.create');
            Route::post('bon_de_commande', [BonDeCommandeController::class, 'store'])->name('bon_de_commande.store');
        });

        // Validate & admin edit
        Route::middleware('role:admin')->group(function () {
            Route::get('bon_de_commande/{bon_de_commande}/validate', [BonDeCommandeController::class, 'edit'])->name('bon_de_commande.edit');
            Route::put('bon_de_commande/{bon_de_commande}', [BonDeCommandeController::class, 'update'])->name('bon_de_commande.update');
        });

        // View (Admin, Superviseur, Manager)
        Route::middleware('role:admin,superviseur,manager')->group(function () {
            Route::get('bon_de_commande', [BonDeCommandeController::class, 'index'])->name('bon_de_commande.index');
            Route::get('bon_de_commande/{bon_de_commande}', [BonDeCommandeController::class, 'show'])->name('bon_de_commande.show');
            Route::get('bon_de_commande/{bon_de_commande}/print', [BonDeCommandeController::class, 'print'])->name('bon_de_commande.print');
            Route::get('bon_de_commande/history', [BonDeCommandeController::class, 'history'])->name('bon_de_commande.history');
        });

        /*
        |-----------------------------------------
        | MAGASINAGE & STOCK (Admin & Superviseur)
        |-----------------------------------------
        */
        Route::middleware('role:admin,superviseur')->group(function () {
            Route::get('magasinage', [MagasinageController::class, 'index'])->name('magasinage.index');
            Route::post('stocks', [MagasinageController::class, 'store'])->name('stocks.store');
            Route::post('stock/transfer', [MagasinageController::class, 'transfer'])->name('stock.transfer');
        });

        /*
        |-----------------------------------------
        | BOOK MANAGEMENT
        |-----------------------------------------
        */

        // All can view (admin / superviseur / manager)
        Route::middleware('role:admin,superviseur,manager')->group(function () {
            Route::get('books/manage', [BookManagementController::class, 'index'])->name('books.manage');
            Route::get('books/{book}/details', [BookController::class, 'show'])->name('books.details');
        });

        // Admin area actions for books
        Route::middleware('auth')->group(function () {

            Route::get('books/stock-alerts', [BookManagementController::class, 'stockAlerts'])->name('books.stock-alerts');
            Route::patch('books/{book}/zone', [BookManagementController::class, 'updateZone'])->name('books.updateZone');
            Route::get('books/import', [BookManagementController::class, 'importForm'])->name('books.import');
            Route::post('books/import', [BookManagementController::class, 'importStore'])->name('books.import.store');

            Route::get('books/archive', [BookController::class, 'archiveList'])->name('books.archiveList');
            Route::post('books/{book}/archive', [BookController::class, 'archive'])->name('books.archive');
            Route::post('books/{book}/unarchive', [BookController::class, 'unarchive'])->name('books.unarchive');

            Route::resource('books', BookController::class)->only([
                'create',
                'store',
                'edit',
                'update',
                'destroy',
            ]);

            Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
            Route::resource('authors', \App\Http\Controllers\Admin\AuthorController::class);
            Route::resource('translators', \App\Http\Controllers\Admin\TranslatorController::class);
            Route::resource('editors', \App\Http\Controllers\Admin\EditorController::class);
            Route::resource('publishers', \App\Http\Controllers\Admin\PublisherController::class);

            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

            Route::get('zones/overview', [ZoneController::class, 'overview'])->name('zones.overview');
            Route::get('zones/{zone}/books', [ZoneController::class, 'booksInZone'])->name('zones.books');
            Route::resource('zones', ZoneController::class);
        });

        /*
        |-----------------------------------------
        | STOCK TRANSFER (ADMIN AREA)
        |-----------------------------------------
        */
        Route::middleware('role:admin,superviseur,manager')->group(function () {
            Route::get('stocks/transfer', [StockTransferController::class, 'index'])->name('stocks.transfer');
            Route::get('stocks/search-book', [StockTransferController::class, 'searchBook'])->name('stocks.search-book');
            Route::post('stocks/transfer', [StockTransferController::class, 'transfer'])->name('stocks.transfer.post');
            Route::get('stocks/sous-zones', [StockTransferController::class, 'getSousZones'])->name('stocks.sous-zones');
            Route::get('stocks/sous-sous-zones', [StockTransferController::class, 'getSousSousZones'])->name('stocks.sous-sous-zones');
            Route::get('stocks/history', [StockTransferController::class, 'history'])->name('stocks.history');
        });
    });

/*
|--------------------------------------------------------------------------
| MANAGER AREA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Manager dashboard via controller
        Route::get('/dashboard', [ManagerDashboard::class, 'index'])
            ->name('dashboard');

        // Manager livres
        Route::get('books/manage', [BookManagementController::class, 'index'])
            ->name('books.manage');
        Route::get('books/{book}/details', [BookController::class, 'show'])
            ->name('books.details');

        // Magasinage
        Route::get('magasinage', [MagasinageController::class, 'index'])
            ->name('magasinage.index');

        // Bon de commande (manager point of view)
        Route::get('bon_de_commande', [BonDeCommandeController::class, 'index'])->name('bon_de_commande.index');
        Route::get('bon_de_commande/create', [BonDeCommandeController::class, 'create'])->name('bon_de_commande.create');
        Route::post('bon_de_commande', [BonDeCommandeController::class, 'store'])->name('bon_de_commande.store');
        Route::get('bon_de_commande/{bon_de_commande}', [BonDeCommandeController::class, 'show'])->name('bon_de_commande.show');
        Route::get('bon_de_commande/{bon_de_commande}/print', [BonDeCommandeController::class, 'print'])->name('bon_de_commande.print');
        Route::get('bon_de_commande/history', [BonDeCommandeController::class, 'history'])->name('bon_de_commande.history');

        Route::get('bon_de_commande/{bon_de_commande}/edit-creator', [BonDeCommandeController::class, 'editCreator'])
            ->name('bon_de_commande.editCreator');
        Route::put('bon_de_commande/{bon_de_commande}/update-creator', [BonDeCommandeController::class, 'updateCreator'])
            ->name('bon_de_commande.updateCreator');

        // Stock transfer (manager area)
        Route::get('stocks/transfer', [StockTransferController::class, 'index'])
            ->name('stocks.transfer');
        Route::post('stocks/transfer', [StockTransferController::class, 'transfer'])
            ->name('stocks.transfer.post');
        Route::get('stocks/history', [StockTransferController::class, 'history'])
            ->name('stocks.history');
        Route::get('stocks/sous-zones', [StockTransferController::class, 'getSousZones'])
            ->name('stocks.sous-zones');
        Route::get('stocks/sous-sous-zones', [StockTransferController::class, 'getSousSousZones'])
            ->name('stocks.sous-sous-zones');
    });

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {

        if (auth()->user()->isCashier()) {
            return redirect()->route('pos.index');
        }

        if (auth()->user()->isAdmin() || auth()->user()->isSuperviseur()) {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->user()->isManager()) {
            return redirect()->route('manager.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |-----------------------------------------
    | POS FRONT (cashier / manager / superviseur)
    |-----------------------------------------
    */
    Route::middleware('role:cashier,manager,superviseur')->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [POSController::class, 'searchBooks'])->name('pos.search');
        Route::get('/pos/book/{code}', [POSController::class, 'getBook'])->name('pos.getBook');
        Route::post('/pos/sale', [POSController::class, 'processSale'])->name('pos.sale');
        Route::get('/pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');
    });
});

require __DIR__.'/auth.php';
