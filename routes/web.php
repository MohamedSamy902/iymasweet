<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\FcmTokenController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/tomorrow', [ShopController::class, 'tomorrow'])->name('shop.tomorrow');
Route::get('/pre-order', [ShopController::class, 'preOrder'])->name('shop.preorder');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
Route::get('/bundle/{slug}', [BundleController::class, 'show'])->name('bundles.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/order/{number}/success', [OrderController::class, 'success'])->name('orders.success');
Route::get('/order/{number}/pay', [OrderController::class, 'pay'])->name('orders.pay');
Route::post('/order/{number}/receipt', [OrderController::class, 'uploadReceipt'])->name('orders.receipt');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{number}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{number}/reorder', [AccountController::class, 'reorder'])->name('orders.reorder');
    Route::get('/favorites', [AccountController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/{product}', [AccountController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/stock-alerts/{product}', [AccountController::class, 'notifyStock'])->name('stock-alerts');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/about', fn () => app(PageController::class)->show('about'))->name('pages.about');
Route::get('/contact', fn () => app(PageController::class)->show('contact'))->name('pages.contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('pages.contact.submit');
Route::get('/faq', fn () => app(PageController::class)->show('faq'))->name('pages.faq');
Route::get('/privacy', fn () => app(PageController::class)->show('privacy'))->name('pages.privacy');
Route::get('/terms', fn () => app(PageController::class)->show('terms'))->name('pages.terms');
Route::get('/refund', fn () => app(PageController::class)->show('refund'))->name('pages.refund');
Route::get('/shipping', fn () => app(PageController::class)->show('shipping'))->name('pages.shipping');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/manifest.webmanifest', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/offline', [PwaController::class, 'offline'])->name('pwa.offline');
Route::get('/api/pwa/firebase-config', [PwaController::class, 'firebaseConfig'])->name('pwa.firebase');

Route::get('/api/chatbot/init', [ChatbotController::class, 'init']);
Route::post('/api/chatbot/message', [ChatbotController::class, 'message']);
Route::post('/api/fcm-tokens', [FcmTokenController::class, 'store']);
Route::delete('/api/fcm-tokens', [FcmTokenController::class, 'destroy']);
