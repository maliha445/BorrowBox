<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BorrowRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Models\Item;



// Homepage with smart search and category filter
Route::get('/', function () {
    $search = trim(request('search', ''));
    $category = trim(request('category', ''));

    $query = Item::query();

    // Category filter (ignore 'All' or empty)
    if ($category !== '' && strtolower($category) !== 'all') {
        $query->where('category', 'like', "%{$category}%");
    }

    // Smart Multi-Token Search
    if ($search !== '') {
        $words = preg_split('/\s+/', strtolower($search), -1, PREG_SPLIT_NO_EMPTY);

        // Student term synonyms & common typo mapping
        $synonymsMap = [
            'laptop' => ['leptop', 'notebook', 'macbook'],
            'leptop' => ['laptop', 'notebook', 'macbook'],
            'calc' => ['calculator', 'scientific', 'fx-991ex'],
            'calculator' => ['calc', 'scientific'],
            'chem' => ['chemistry', 'chemical'],
            'chemistry' => ['chem', 'chemical'],
            'bio' => ['biology'],
            'book' => ['books', 'textbook'],
            'books' => ['book', 'textbook'],
            'racket' => ['racquet', 'badminton'],
            'badminton' => ['racket', 'shuttlecock'],
        ];

        $expandedWords = $words;
        foreach ($words as $w) {
            if (isset($synonymsMap[$w])) {
                $expandedWords = array_unique(array_merge($expandedWords, $synonymsMap[$w]));
            }
        }

        $query->where(function ($q) use ($words, $expandedWords, $search) {
            // 1. Exact phrase match across all fields
            $q->where(function ($phraseQ) use ($search) {
                $phraseQ->where('item_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('condition', 'like', "%{$search}%");
            });

            // 2. All words must match (e.g. "casio" in title, "calculator" in category)
            if (count($words) > 1) {
                $q->orWhere(function ($allWordsQ) use ($words) {
                    foreach ($words as $word) {
                        $allWordsQ->where(function ($subQ) use ($word) {
                            $subQ->where('item_name', 'like', "%{$word}%")
                                 ->orWhere('description', 'like', "%{$word}%")
                                 ->orWhere('category', 'like', "%{$word}%")
                                 ->orWhere('owner_name', 'like', "%{$word}%")
                                 ->orWhere('condition', 'like', "%{$word}%");
                        });
                    }
                });
            }

            // 3. Synonym matches
            foreach ($expandedWords as $syn) {
                $q->orWhere('item_name', 'like', "%{$syn}%")
                  ->orWhere('description', 'like', "%{$syn}%")
                  ->orWhere('category', 'like', "%{$syn}%");
            }
        });
    }

    $items = $query->latest()->get();

    return view('welcome', compact('items', 'search', 'category'));
})->name('home');

// Public Item Routes
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// Protected Item Routes (Require Login & Verified Student Status)
Route::middleware(['auth', 'verified.student'])->group(function () {
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Profile & Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [DashboardController::class, 'history'])->name('history');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'post'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Reviews
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Reports
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});
Route::get('/users/{user}', [ProfileController::class, 'showUser'])->name('users.show');

// Borrow Request Routes
Route::middleware(['auth', 'verified.student'])->group(function () {
    Route::post('/borrow-requests', [BorrowRequestController::class, 'store'])->name('borrow-requests.store');
    Route::post('/borrow-requests/{borrowRequest}/cancel', [BorrowRequestController::class, 'cancel'])->name('borrow-requests.cancel');
    Route::post('/borrow-requests/{borrowRequest}/approve', [BorrowRequestController::class, 'approve'])->name('borrow-requests.approve');
    Route::post('/borrow-requests/{borrowRequest}/reject', [BorrowRequestController::class, 'reject'])->name('borrow-requests.reject');
    Route::post('/borrow-requests/{borrowRequest}/return', [BorrowRequestController::class, 'returnConfirm'])->name('borrow-requests.return');
});

// Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/verify', [AdminController::class, 'approveVerification'])->name('users.verify');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::post('/users/{user}/reactivate', [AdminController::class, 'reactivateUser'])->name('users.reactivate');
    Route::get('/items', [AdminController::class, 'items'])->name('items');
    Route::delete('/items/{item}', [AdminController::class, 'destroyItem'])->name('items.destroy');
    Route::get('/borrowings', [AdminController::class, 'borrowings'])->name('borrowings');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{report}/update', [AdminController::class, 'updateReport'])->name('reports.update');
});
