<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Models\Item;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

// Item Routes
Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
