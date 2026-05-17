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
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserMembershipController;
use App\Http\Controllers\HighlightController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CoinPackageController;
use App\Http\Controllers\ServicePriceController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\LocationController;
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

// ================= GUEST ONLY ROUTES (Belum Login) =================
Route::middleware(['guest'])->group(function () {
    // Menampilkan halaman / HTML
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    
    // Memproses Data
    Route::post('/login', [SesiController::class, 'login']);
    Route::post('/register', [SesiController::class, 'register'])->name('register');

    // OTP
    Route::post('/send-otp', [SesiController::class, 'sendOtp'])->name('send-otp');
    Route::post('/verify-otp', [SesiController::class, 'verifyOtp'])->name('verify-otp');
});

// ================= AUTHENTICATED ROUTES =================
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    // Mencari saran alamat (Autocomplete)
    Route::get('/search', [LocationController::class, 'searchAddress'])->name('location.search');
    // Mencari alamat berdasarkan koordinat (Reverse Geocode)
    Route::get('/reverse-geocode', [LocationController::class, 'reverseGeocode'])->name('location.reverse_geocode');
});

// ================= USER/AGENT ROUTES =================
Route::middleware(['auth'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // Top Up Koin 
    Route::get('/topup', [TopupController::class, 'index'])->name('user.topup.index');
    Route::post('/topup/initiate', [TopupController::class, 'initiatePayment'])->name('user.topup.initiate');
    Route::get('/topup/status/{orderId}', [TopupController::class, 'checkStatus'])->name('user.topup.status');

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

        // Highlight
        Route::get('/highlight/search-properties', [HighlightController::class, 'searchProperties'])->name('user.highlight.search.properties');

    });

    // Logout
    Route::post('/logout', [SesiController::class, 'logout'])->name('logout');

    // Membership
    Route::get('/user/membership', [UserMembershipController::class, 'index'])->name('user.membership.index');

    // History / Trash
    Route::get('/properties/trash', [TrashController::class, 'index'])->name('user.properties.trash');
    Route::patch('/properties/{id}/restore', [TrashController::class, 'restore'])->name('user.properties.restore');
});

// ================= ADMIN ROUTES (Hanya Role Admin) =================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard Admin + notifikasi
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/report', fn() => view('admin.report'))->name('report');

    // User Management
    Route::get('/list-user', [SesiController::class, 'list'])->name('user');
    Route::post('/register/delete/{id}', [SesiController::class, 'deleteUser'])->name('deleteuser');
    Route::get('/list-users', [UserController::class, 'list'])->name('admin.list.users');

    // Management Wallet
    Route::patch('/update/wallet', [UserController::class, 'updateWallet'])->name('admin.update.wallet');

    // Audit 
    Route::get('/audit-log', [AuditController::class, 'index'])->name('admin.audit.index');

    // Admin Register
    Route::get('/register', [SesiController::class, 'createAdmin'])->name('admin.register');
    Route::post('/store', [SesiController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/{id}/edit', [SesiController::class, 'editAdmin'])->name('admin.edit');
    Route::put('/{id}/update', [SesiController::class, 'updateAdmin'])->name('admin.update');
    Route::post('/{id}/delete', [SesiController::class, 'deleteAdmin'])->name('admin.delete');

    // Highlight
    Route::get('/highlight', [HighlightController::class, 'index'])->name('admin.highlight.index');
    Route::get('/highlight/create', fn() => view('admin.highlight.form'))->name('admin.highlight.create');
    Route::post('/highlight/store', [HighlightController::class, 'store'])->name('admin.highlight.store');
    Route::post('/highlight/sundul', [HighlightController::class, 'sundul'])->name('admin.highlight.sundul');
    Route::delete('/highlight/destroy/{type}/{property_id}', [HighlightController::class, 'destroy'])->name('admin.highlight.destroy');
    Route::get('/highlight/search-agents', [HighlightController::class, 'searchAgents'])->name('admin.highlight.search.agents');

    // Membership
    Route::get('/membership', [MembershipController::class, 'index'])->name('admin.membership.index');
    Route::post('/membership/{id}', [MembershipController::class, 'update'])->name('admin.membership.update');

    // --- FITUR MEMBERSHIP ---
    // 1. Halaman Daftar/Manajemen Pelanggan (Memanggil fungsi 'userList')
    Route::get('/membership/user-list', [MembershipController::class, 'userList'])->name('admin.membership.user-list');

    // History / Trash
    Route::get('/properties/trash', [TrashController::class, 'index'])->name('admin.properties.trash');
    Route::patch('/properties/{id}/restore', [TrashController::class, 'restore'])->name('admin.properties.restore');

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
    });

    // Nanti perlu perbaikan
    Route::patch('/banner/${bannerId}/toggle-status', [BannerController::class, 'toggleStatus'])->name('admin.banner.toggle-status');

    // CRUD Contact
    Route::resource('contacts', ContactController::class);
    // Route::post('portofolios', PortofolioController::class, 'update')->name('portofolios.update');
    Route::resource('portofolios', PortofolioController::class);
    Route::resource('testimonis', TestimoniController::class);
    Route::resource('banner', BannerController::class);
    

    // ================= MANAJEMEN PAKET KOIN =================
    Route::get('/koin', [CoinPackageController::class, 'index'])->name('admin.koin.index');
    Route::post('/koin', [CoinPackageController::class, 'store'])->name('admin.koin.store');
    Route::put('/koin/{id}/update', [CoinPackageController::class, 'update'])->name('admin.koin.update');
    Route::delete('/koin/{id}/destroy', [CoinPackageController::class, 'destroy'])->name('admin.koin.destroy');

    // ================= MANAJEMEN HARGA LAYANAN =================
    Route::post('/service-price/store', [ServicePriceController::class, 'store'])->name('admin.service.price.store');
    Route::patch('/service-price/{id}/update', [ServicePriceController::class, 'update'])->name('admin.service.price.update');
    Route::delete('/service-price/{id}/destroy', [ServicePriceController::class, 'destroy'])->name('admin.service.price.destroy');
});