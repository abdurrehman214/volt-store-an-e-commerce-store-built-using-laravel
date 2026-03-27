<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;  // ✅ FIX: was missing — caused "Undefined type UserController" in IDE

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES — no login required
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// ═══════════════════════════════════════════════════════════════════════════
// CART ROUTES — session-based, guests allowed
// ═══════════════════════════════════════════════════════════════════════════

// ✅ KEPT your cart routes exactly
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// ✅ NEW — AJAX endpoint for navbar badge count refresh
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// ═══════════════════════════════════════════════════════════════════════════
// STATIC PAGES — footer links
// ═══════════════════════════════════════════════════════════════════════════

// ✅ KEPT all your static pages
Route::get('/contact',       fn() => view('pages.contact'))->name('contact');
Route::get('/faq',           fn() => view('pages.faq'))->name('faq');
Route::get('/about',         fn() => view('pages.about'))->name('page.about');
Route::get('/privacy',       fn() => view('pages.privacy'))->name('page.privacy');
Route::get('/terms',         fn() => view('pages.terms'))->name('page.terms');
Route::get('/shipping',      fn() => view('pages.shipping'))->name('page.shipping');
Route::get('/returns',       fn() => view('pages.returns'))->name('page.returns');
Route::get('/sizing',        fn() => view('pages.sizing'))->name('page.sizing');
Route::get('/wholesale',     fn() => view('pages.wholesale'))->name('page.wholesale');
Route::get('/accessibility', fn() => view('pages.accessibility'))->name('page.accessibility');
Route::get('/careers',       fn() => view('pages.careers'))->name('page.careers');

// ✅ KEPT newsletter — just returns JSON now so it works with AJAX too
Route::post('/newsletter/subscribe', function () {
    request()->validate(['email' => 'required|email']);
    // TODO: save to newsletter_subscribers table
    if (request()->ajax()) {
        return response()->json(['success' => true, 'message' => 'Thanks for subscribing!']);
    }
    return back()->with('success', 'Thanks for subscribing!');
})->name('newsletter.subscribe');

// ═══════════════════════════════════════════════════════════════════════════
// AUTH ROUTES — login, register, logout, password reset
// ═══════════════════════════════════════════════════════════════════════════

// ✅ KEPT — auth.php handles all authentication routes
require __DIR__.'/auth.php';

// ═══════════════════════════════════════════════════════════════════════════
// CUSTOMER ROUTES — login required
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware(['auth'])->group(function () {

    // Profile
    // ✅ NEW — UserController now has proper profile methods
    Route::get('/profile',           [UserController::class, 'profile'])->name('profile');
    Route::put('/profile',           [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password',  [UserController::class, 'updatePassword'])->name('profile.password');

    // Checkout
    // ✅ FIX: create() method was missing in old OrderController — now exists
    Route::get('/checkout',  [OrderController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // Orders
    // ✅ KEPT your order routes
    Route::get('/my-orders',            [OrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/order/{id}',           [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Returns
    // ✅ KEPT your return routes
    Route::get('/order/{id}/return',  [OrderController::class, 'createReturn'])->name('orders.return.create');
    Route::post('/order/{id}/return', [OrderController::class, 'storeReturn'])->name('orders.return.store');
});

// ═══════════════════════════════════════════════════════════════════════════
// ADMIN + EMPLOYEE ROUTES — role protected
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin,employee'])->group(function () {

    // Dashboard
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ── Orders ──────────────────────────────────────────────────────────────
    // ✅ KEPT all your order management routes
    Route::get('/admin/orders',                        [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/{id}',                    [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/admin/order/{id}/clear-payment',     [AdminController::class, 'clearPayment'])->name('admin.payment.clear');
    Route::post('/admin/order/{id}/dispatch',          [AdminController::class, 'dispatchOrder'])->name('admin.order.dispatch');
    Route::delete('/admin/order/{id}/cancel',          [AdminController::class, 'cancelOrder'])->name('admin.order.cancel');

    // ── Products ─────────────────────────────────────────────────────────────
    // ✅ KEPT your existing product routes
    Route::get('/admin/products',             [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/{id}/edit',    [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/admin/product/{id}',         [AdminController::class, 'updateProduct'])->name('admin.products.update');

    // ✅ NEW — createProduct/storeProduct methods added in Step 3
    Route::get('/admin/products/create',      [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/admin/products',            [AdminController::class, 'storeProduct'])->name('admin.products.store');

    // ── Stock ────────────────────────────────────────────────────────────────
    Route::get('/admin/stock', [AdminController::class, 'stock'])->name('admin.stock');

    // ── Returns ──────────────────────────────────────────────────────────────
    // ✅ KEPT all your return routes
    Route::get('/admin/returns',                    [AdminController::class, 'returns'])->name('admin.returns');
    Route::get('/admin/return/{id}',                [AdminController::class, 'showReturn'])->name('admin.returns.show');
    Route::post('/admin/return/{id}/process',       [AdminController::class, 'processReturn'])->name('admin.returns.process');

    // ── Feedback ─────────────────────────────────────────────────────────────
    Route::get('/admin/feedback',              [AdminController::class, 'feedback'])->name('admin.feedback');
    // ✅ NEW — toggle review visibility
    Route::post('/admin/feedback/{id}/toggle', [AdminController::class, 'toggleFeedback'])->name('admin.feedback.toggle');

    // ── Employees — admin only ───────────────────────────────────────────────
    // ✅ KEPT your nested role:admin group
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/employees',        [AdminController::class, 'employees'])->name('admin.employees');
        Route::get('/admin/employees/create', [AdminController::class, 'createEmployee'])->name('admin.employees.create');
        Route::post('/admin/employees',       [AdminController::class, 'storeEmployee'])->name('admin.employees.store');
        // ✅ NEW — delete employee account
        Route::delete('/admin/employees/{id}', [AdminController::class, 'destroyEmployee'])->name('admin.employees.destroy');
    });
});