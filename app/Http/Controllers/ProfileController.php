<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->take(10)->get();
        $items = $user->items()->latest()->take(6)->get();

        return view('profile.show', compact('user', 'reviews', 'items'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'student_id' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'id_card_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->student_id = $validated['student_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('id_card_image')) {
            $file = $request->file('id_card_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/id_cards');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $user->id_card_image = 'uploads/id_cards/' . $filename;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Public profile of a user.
     */
    public function showUser(User $user)
    {
        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->paginate(10);
        $items = $user->items()->where('is_available', true)->latest()->get();

        return view('profile.public', compact('user', 'reviews', 'items'));
    }
}

