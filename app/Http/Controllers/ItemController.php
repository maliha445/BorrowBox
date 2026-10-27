<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Show form for creating a new item.
     */
    public function create()
    {
        $defaultOwner = Auth::check() ? Auth::user()->name : '';
        return view('items.create', compact('defaultOwner'));
    }

    /**
     * Store a newly created item in database.
     */
    public function store(Request $request)
    {
        $rules = [
            'item_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'condition' => 'nullable|string|max:50',
            'owner_name' => 'required|string|max:255',
        ];

        $validated = $request->validate($rules);

        $validated['reward_coins'] = 100;
        $validated['is_available'] = true;

        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
            if (empty($validated['owner_name'])) {
                $validated['owner_name'] = Auth::user()->name;
            }
        }

        Item::create($validated);

        return redirect()
            ->route('items.create')
            ->with('success', 'Item "' . $validated['item_name'] . '" added successfully! You earned 100 coins.');
    }
}
