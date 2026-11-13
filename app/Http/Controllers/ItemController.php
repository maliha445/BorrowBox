<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    /**
     * Display a listing of items with search and filters.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $category = trim($request->input('category', ''));
        $status = trim($request->input('status', ''));

        $query = Item::with(['user', 'categoryRelation'])->latest();

        // Search by item name or description
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category !== '' && strtolower($category) !== 'all') {
            $query->where(function ($q) use ($category) {
                $q->where('category', 'like', "%{$category}%")
                  ->orWhereHas('categoryRelation', function ($sub) use ($category) {
                      $sub->where('name', 'like', "%{$category}%")
                          ->orWhere('slug', 'like', "%{$category}%");
                  });
            });
        }

        // Availability status filter
        if ($status === 'available') {
            $query->where('is_available', true)->where('status', 'available');
        } elseif ($status === 'borrowed') {
            $query->where('status', 'borrowed');
        } elseif ($status === 'overdue') {
            $query->where('status', 'overdue');
        }

        $items = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('items.index', compact('items', 'categories', 'search', 'category', 'status'));
    }

    /**
     * Show form for creating a new item.
     */
    public function create()
    {
        $defaultOwner = Auth::check() ? Auth::user()->name : '';
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('defaultOwner', 'categories'));
    }

    /**
     * Store a newly created item in database.
     */
    public function store(Request $request)
    {
        $rules = [
            'item_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'condition' => 'nullable|string|max:50',
            'owner_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ];

        $validated = $request->validate($rules);

        $validated['reward_coins'] = 100;
        $validated['is_available'] = true;
        $validated['status'] = 'available';

        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
            if (empty($validated['owner_name'])) {
                $validated['owner_name'] = Auth::user()->name;
            }
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/items');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $validated['image'] = 'uploads/items/' . $filename;
        }

        if (!empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            if ($cat) {
                $validated['category'] = $cat->name;
            }
        } elseif (empty($validated['category'])) {
            $validated['category'] = 'Other';
        }

        Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item "' . $validated['item_name'] . '" added successfully! You earned 100 coins.');
    }

    /**
     * Display the specified item details.
     */
    public function show(Item $item)
    {
        $item->load(['user.reviewsReceived', 'categoryRelation']);
        $reviews = $item->reviews()->with('reviewer')->latest()->take(6)->get();

        return view('items.show', compact('item', 'reviews'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        if ($item->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to edit this item.');
        }

        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item in database.
     */
    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to edit this item.');
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'condition' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'status' => 'nullable|string|in:available,borrowed,returned,overdue',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/items');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $validated['image'] = 'uploads/items/' . $filename;
        }

        if (!empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            if ($cat) {
                $validated['category'] = $cat->name;
            }
        }

        if (isset($validated['status'])) {
            $validated['is_available'] = ($validated['status'] === 'available');
        }

        $item->update($validated);

        return redirect()->route('items.show', $item)->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from database.
     */
    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to delete this item.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
