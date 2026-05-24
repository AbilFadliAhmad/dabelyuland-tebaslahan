<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\PortofolioController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\SesiController;
use App\Http\Controllers\Admin\TestimoniController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PortofoliodepanController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\UserMembershipController;
use App\Http\Controllers\HighlightController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CoinPackageController;
use App\Http\Controllers\ServicePriceController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\UserQuestController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// ================= FRONTEND ROUTES =================
Route::get('/', [HomeController::class, 'index'])->name('home');
    
// Halaman Utama yang akan dilihat pengunjung
Route::get('/property/{property:slug}', [HomeController::class, 'show'])->name('home.property-details');
Route::get('/search/detail/{type}/{id}', [SearchController::class, 'show'])->name('shop.property-details');
Route::get('/about', fn() => view('frontside.about'))->name('about'); // Tentang
Route::get('/faq', fn() => view('frontside.faq'))->name('faq'); // FAQ
Route::get('/portfolio', [PortofoliodepanController::class, 'index'])->name('portfolio.index'); // Portofolio
Route::get('/search', [SearchController::class, 'index'])->name('shop.index');
Route::get('/search/loadmore', [SearchController::class, 'loadMore'])->name('shop.load-more');
Route::get('/contact', [KontakController::class, 'index'])->name('contact'); // KOntak
Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi'); // KOntak

// News
Route::get('/news/{slug}', [NewsController::class, 'index'])->name('news.show');

// Analytics Tracking
Route::post('/track-view-website', [AnalyticsController::class, 'trackViewWebsite'])->name('track-view-website');
Route::post('/track-view-property', [AnalyticsController::class, 'trackViewProperty'])->name('track-view-property');
Route::post('/track-click-whatsapp', [AnalyticsController::class, 'trackClickWhatsapp'])->name('track-click-whatsapp');

// Manajemen Quest Share & Unique View
Route::post('/quest/unique-view', [UserQuestController::class, 'uniqueViewProperty'])->name('quest.unique-view');
Route::post('/quest/share-property', [UserQuestController::class, 'shareProperty'])->name('quest.share-property');


// Fitur Favorite
Route::post('/favorite-list', [FavoriteController::class, 'getProperties'])->name('favorite-list');

// ================= GUEST ONLY ROUTES (Belum Login) =================
Route::middleware(['guest'])->group(function () {
    // Menampilkan halaman / HTML
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    
    // Memproses Data
    Route::post('/login', [SesiController::class, 'login']);
    Route::post('/register', [SesiController::class, 'register'])->name('register');

    // Pengiriman OTP (Pendaftaran & Resend Lupa Sandi)
    Route::post('/send-otp', [SesiController::class, 'sendOtp'])->name('send-otp');

    //  Lupa Sandi
    Route::post('/forgot-password-req', [SesiController::class, 'forgotPasswordReq'])->name('forgot-password-req');
    Route::post('/verify-forgot-otp', [SesiController::class, 'verifyForgotOtp'])->name('verify-forgot-otp');
    Route::post('/reset-password', [SesiController::class, 'resetPassword'])->name('reset-password');

    // Fitur FCM
    Route::post('/subscribe-topic', [FCMController::class, 'subscribeTopic'])->name('subscribe-topic');
    Route::post('/unsubscribe-topic', [FCMController::class, 'unsubscribeTopic'])->name('unsubscribe-topic');
    // Route::post('/send-notification', [FCMController::class, 'sendNotification'])->name('send-notification');

    // Fitur Notifikasi FCM
    Route::post('/subscribe-notification-topics', [FCMController::class, 'subscribeNotificationTopics'])->name('subscribe-notification-topics');
    Route::post('/send-notification-new-property', [FCMController::class, 'sendNotificationNewProperty'])->name('send-notification-new-property');
    Route::post('/toggle-notification', [FCMController::class, 'toggleNotification'])->name('toggle-notification');
});

// ================= AUTHENTICATED ROUTES =================
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    // Mencari saran alamat (Autocomplete)
    Route::get('/search', [LocationController::class, 'searchAddress'])->name('location.search');
    // Mencari alamat berdasarkan koordinat (Reverse Geocode)
    Route::get('/reverse-geocode', [LocationController::class, 'reverseGeocode'])->name('location.reverse_geocode');

    // Logout
    Route::post('/logout', [SesiController::class, 'logout'])->name('logout');

    // Archive/Trash
    Route::get('/properties/archive', [ArchiveController::class, 'index'])->name('properties.archive');
    Route::patch('/properties/{id}/restore', [ArchiveController::class, 'restore'])->name('properties.restore');

    // Highlight
    Route::get('/highlight', [HighlightController::class, 'index'])->name('highlight.index');
    Route::get('/highlight/create', [HighlightController::class, 'create'])->name('highlight.create');
    Route::post('/highlight/store', [HighlightController::class, 'store'])->name('highlight.store');
    Route::post('/highlight/sundul', [HighlightController::class, 'sundul'])->name('highlight.sundul');
    Route::delete('/highlight/destroy/{type}/{property_id}', [HighlightController::class, 'destroy'])->name('highlight.destroy');
    Route::get('/highlight/search-properties', [HighlightController::class, 'searchProperties'])->name('highlight.search.properties');

    // Banner
    Route::resource('banner', BannerController::class);
    Route::patch('/banner/${bannerId}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banner.toggle-status');
    Route::post('/banner/generate-ai', [BannerController::class, 'generateAi'])->name('banner.generateAi');
    Route::post('/banner/generate-ai-kombinasi', [BannerController::class, 'generateAiKombinasi'])->name('banner.generateAiKombinasi');

    // Notifikasi
    Route::get('notification/check', [NotificationController::class, 'checkNotifications'])->name('notification.check');
    Route::get('notification/list', [NotificationController::class, 'listNotifications'])->name('notification.list');
    Route::delete('notification/destroy', [NotificationController::class, 'destroyNotification'])->name('notification.destroy');

    // Transaksi
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transaction/refund/{id}', [TransactionController::class, 'refund'])->name('transaction.refund');

});

// ================= USER/AGENT ROUTES =================
Route::middleware(['auth'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // Quest Manajemen
    Route::get('/user/quest', [UserQuestController::class, 'index'])->name('user.quest.index');
    Route::post('/user/quests/{id}/claim', [UserQuestController::class, 'claim'])->name('user.quest.claim');
    Route::post('/user/quests/init', [UserQuestController::class, 'initQuest'])->name('user.quest.init');

    // Top Up Koin 
    Route::get('/user/topup', [TopupController::class, 'index'])->name('user.topup.index');
    Route::post('/user/topup/initiate', [TopupController::class, 'initiatePayment'])->name('user.topup.initiate');
    Route::get('/user/topup/status/{orderId}', [TopupController::class, 'checkStatus'])->name('user.topup.status');

    // Membership
    Route::get('/user/membership', [UserMembershipController::class, 'index'])->name('user.membership.index');
    Route::post('/user/membership/initiate', [UserMembershipController::class, 'initiatePayment'])->name('user.membership.initiate');
    Route::get('/user/membership/status/{orderId}', [UserMembershipController::class, 'checkStatus'])->name('user.membership.status');
    Route::post('/user/membership/cancel', [UserMembershipController::class, 'cancelPayment'])->name('user.membership.cancel');

    // CRUD Property
    Route::prefix('/user/property')->group(function () {
        // List
        Route::get('/buildings', [PropertyController::class, 'index'])->name('user.buildings.index'); // Halaman List khusus bangunan
        Route::get('/lands', [PropertyController::class, 'index'])->name('user.lands.index'); // Halaman List khusus tanah

        // Create
        Route::get('/create/{typeProperty}', [PropertyController::class, 'create'])->name('user.property.create'); // Halaman Tambah Properti (tanah, bangunan)
        Route::post('/store', [PropertyController::class, 'store'])->name('user.property.store'); // Memproses Tambah Properti (tanah, bangunan)
        Route::post('/upload/image', [PropertyController::class, 'uploadImage'])->name('user.property.uploadImage'); // Memproses Upload gambar

        // Edit
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('user.property.edit'); // Halaman Edit Properti (tanah, bangunan)
        Route::post('/update', [PropertyController::class, 'update'])->name('user.property.update'); // Memproses Edit Properti (tanah, bangunan)

        // Delete
        Route::delete('/{property}/delete', [PropertyController::class, 'destroy'])->name('user.property.destroy');
        Route::post('/deleteImage', [PropertyController::class, 'deleteImage'])->name('user.property.deleteImage');


        // Dijual / Disewa
        Route::patch('/{property}/toggle-availability', [PropertyController::class, 'toggleAvailability'])->name('user.property.toggle-availability');

        // Mengaktifkan atau menonaktifkan properti
        Route::patch('/{property}/toggle-visibility', [PropertyController::class, 'toggleVisibility'])->name('user.property.toggle-visibility');
    });
});

// ================= ADMIN ROUTES (Hanya Role Admin) =================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Highlight
    Route::get('/highlight/search-agents', [HighlightController::class, 'searchAgents'])->name('admin.highlight.search.agents');

    // Quest Manajemen
    Route::get('/quest', [QuestController::class, 'index'])->name('admin.quest.index');
    Route::patch('/quest/{id}', [QuestController::class, 'update'])->name('admin.quest.update');

    // Dashboard Admin + notifikasi
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    // Route::get('/list-user', [SesiController::class, 'list'])->name('user');
    Route::post('/register/delete/{id}', [SesiController::class, 'deleteUser'])->name('deleteuser');
    Route::get('/list-users', [UserController::class, 'list'])->name('admin.list.users');

    // Management Wallet
    Route::patch('/update/wallet', [UserController::class, 'updateWallet'])->name('admin.update.wallet');

    // Audit 
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('admin.audit.index');

    // Admin Register
    Route::get('/register', [SesiController::class, 'createAdmin'])->name('admin.register');
    Route::post('/store', [SesiController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/{id}/edit', [SesiController::class, 'editAdmin'])->name('admin.edit');
    Route::put('/{id}/update', [SesiController::class, 'updateAdmin'])->name('admin.update');
    Route::post('/{id}/delete', [SesiController::class, 'deleteAdmin'])->name('admin.delete');

    // Membership
    Route::get('/membership', [MembershipController::class, 'index'])->name('admin.membership.index');
    Route::post('/membership/{id}', [MembershipController::class, 'update'])->name('admin.membership.update');

    // --- FITUR MEMBERSHIP ---
    // 1. Halaman Daftar/Manajemen Pelanggan (Memanggil fungsi 'userList')
    Route::get('/membership/user-list', [MembershipController::class, 'userList'])->name('admin.membership.user-list');

    // CRUD Property
    Route::prefix('/property')->group(function () {
        // List
        Route::get('/buildings', [PropertyController::class, 'index'])->name('admin.buildings.index'); // Halaman List khusus bangunan
        Route::get('/lands', [PropertyController::class, 'index'])->name('admin.lands.index'); // Halaman List khusus tanah

        // Create
        Route::get('/create/{typeProperty}', [PropertyController::class, 'create'])->name('admin.property.create'); // Halaman Tambah Properti (tanah, bangunan)
        Route::post('/store', [PropertyController::class, 'store'])->name('admin.property.store'); // Memproses Tambah Properti (tanah, bangunan)
        Route::post('/upload/image', [PropertyController::class, 'uploadImage'])->name('admin.property.uploadImage'); // Memproses Upload gambar

        // Edit
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('admin.property.edit'); // Halaman Edit Properti (tanah, bangunan)
        Route::post('/update', [PropertyController::class, 'update'])->name('admin.property.update'); // Memproses Edit Properti (tanah, bangunan)

        // Delete
        // Route::delete('/{property}/deleteImage', [PropertyController::class, 'deleteImage'])->name('admin.property.deleteImage');
        Route::patch('/{property}/archive', [PropertyController::class, 'archive'])->name('admin.property.archive');

        // Dijual / Disewa
        Route::patch('/{property}/toggle-availability', [PropertyController::class, 'toggleAvailability'])->name('admin.property.toggle-availability');

        // Mengaktifkan atau menonaktifkan properti
        Route::patch('/{property}/toggle-visibility', [PropertyController::class, 'toggleVisibility'])->name('admin.property.toggle-visibility');

        // Verifikasi Properti
        Route::patch('/{property}/verify-property', [PropertyController::class, 'verifyProperty'])->name('admin.property.verify-property');

        // Verifikasi Banner
        Route::post('banner/verify-banner', [BannerController::class, 'verifyBanner'])->name('admin.banner.verify-banner');
    });

    // CRUD Contact
    Route::resource('contacts', ContactController::class);
    // Route::post('portofolios', PortofolioController::class, 'update')->name('portofolios.update');
    Route::resource('portofolios', PortofolioController::class);
    Route::resource('testimonis', TestimoniController::class);
    

    // ================= MANAJEMEN PAKET KOIN =================
    Route::get('/koin', [CoinPackageController::class, 'index'])->name('admin.koin.index');
    Route::post('/koin', [CoinPackageController::class, 'store'])->name('admin.koin.store');
    Route::put('/koin/{id}/update', [CoinPackageController::class, 'update'])->name('admin.koin.update');
    Route::delete('/koin/{id}/destroy', [CoinPackageController::class, 'destroy'])->name('admin.koin.destroy');

    // ================= MANAJEMEN HARGA LAYANAN =================
    Route::post('/service-price/store', [ServicePriceController::class, 'store'])->name('admin.service.price.store');
    Route::patch('/service-price/{id}/update', [ServicePriceController::class, 'update'])->name('admin.service.price.update');
    Route::delete('/service-price/{id}/destroy', [ServicePriceController::class, 'destroy'])->name('admin.service.price.destroy');

    // ================= MANAJEMEN HARGA LAYANAN =================
    Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report');
});