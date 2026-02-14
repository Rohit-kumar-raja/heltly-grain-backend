<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get("/dashboard", function () {
        return redirect('/admin/dashboard');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/products/data', [\App\Http\Controllers\Admin\ProductController::class, 'getData'])->name('products.data');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

        Route::get('/orders/data', [\App\Http\Controllers\Admin\OrderController::class, 'getData'])->name('orders.data');
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'update']);

        Route::get('/wishlists/data', [\App\Http\Controllers\Admin\WishlistController::class, 'getData'])->name('wishlists.data');
        Route::get('/wishlists/stats', [\App\Http\Controllers\Admin\WishlistController::class, 'stats'])->name('wishlists.stats');
        Route::resource('wishlists', \App\Http\Controllers\Admin\WishlistController::class)->only(['index', 'destroy']);


        // Rules Engine
        Route::get('/meal-plan/rules/data', [\App\Http\Controllers\Admin\MealPlanRuleController::class, 'getData'])->name('meal-plan.rules.data');
        Route::resource('meal-plan/rules', \App\Http\Controllers\Admin\MealPlanRuleController::class, ['names' => 'meal-plan.rules']);

        Route::get('/app-users/data', [\App\Http\Controllers\Admin\AppUserController::class, 'getData'])->name('app-users.data');
        Route::get('/app-users/{id}/diet-preview', [\App\Http\Controllers\Admin\AppUserController::class, 'dietPreview'])->name('app-users.diet-preview');
        Route::resource('app-users', \App\Http\Controllers\Admin\AppUserController::class)->only(['index', 'show']);

        Route::get('/transactions/data', [\App\Http\Controllers\Admin\TransactionController::class, 'getData'])->name('transactions.data');
        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)->only(['index']);

        Route::get('/categories/data', [\App\Http\Controllers\Admin\CategoryController::class, 'getData'])->name('categories.data');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        // Admin Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');
        Route::post('/notifications/clear', [\App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('admin.notifications.clear');
    });
});

require __DIR__ . '/auth.php';

Route::get('/storage-link', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage link created successfully.';
});

Route::get('/debug-storage', function () {
    $info = [];
    $publicStorage = public_path('storage');
    $storageAppPublic = storage_path('app/public');

    $info['paths'] = [
        'public_path' => public_path(),
        'storage_path_configured' => $storageAppPublic,
        'symlink_loc' => $publicStorage,
    ];

    $info['symlink_check'] = [
        'exists' => file_exists($publicStorage) ? 'Yes' : 'No',
        'is_link' => is_link($publicStorage) ? 'Yes' : 'No',
        'target' => is_link($publicStorage) ? readlink($publicStorage) : 'N/A',
    ];

    // precision check: create a file
    try {
        $testFilename = 'debug_test_' . time() . '.txt';
        \Illuminate\Support\Facades\Storage::disk('public')->put($testFilename, 'This is a test file.');

        $realPathViaStorage = $storageAppPublic . '/' . $testFilename;
        $realPathViaSymlink = $publicStorage . '/' . $testFilename;

        $info['file_check'] = [
            'created' => 'Yes',
            'filename' => $testFilename,
            'perms_via_storage' => substr(sprintf('%o', fileperms($realPathViaStorage)), -4),
            'readable_via_symlink' => is_readable($realPathViaSymlink) ? 'Yes' : 'No',
            'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($testFilename),
        ];
    } catch (\Exception $e) {
        $info['file_check'] = [
            'created' => 'No',
            'error' => $e->getMessage(),
        ];
    }

    return $info;
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Routes, Config, Cache, and View cache cleared successfully.';
});
