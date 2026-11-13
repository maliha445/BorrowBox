<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Show the form for creating a new review.
     */
    public function create(Request $request)
    {
        $borrowRequestId = $request->query('borrow_request_id');
        $borrowRequest = BorrowRequest::with(['item', 'borrower', 'owner'])->findOrFail($borrowRequestId);

        $user = Auth::user();

        // Must be borrower or owner
        if ($borrowRequest->borrower_id !== $user->id && $borrowRequest->owner_id !== $user->id) {
            abort(403, 'You are not part of this borrowing request.');
        }

        // Must be returned
        if ($borrowRequest->status !== 'returned') {
            return redirect()->route('dashboard')->with('error', 'Reviews can only be submitted after the item has been returned.');
        }

        // Check if already reviewed
        $alreadyReviewed = Review::where('borrow_request_id', $borrowRequest->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('dashboard')->with('info', 'You have already reviewed this borrowing.');
        }

        $isBorrower = ($borrowRequest->borrower_id === $user->id);
        $reviewee = $isBorrower ? $borrowRequest->owner : $borrowRequest->borrower;

        return view('reviews.create', compact('borrowRequest', 'reviewee', 'isBorrower'));
    }

    /**
     * Store a newly created review in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrow_request_id' => 'required|exists:borrow_requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $borrowRequest = BorrowRequest::with(['item', 'borrower', 'owner'])->findOrFail($validated['borrow_request_id']);

        if ($borrowRequest->borrower_id !== $user->id && $borrowRequest->owner_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($borrowRequest->status !== 'returned') {
            return back()->with('error', 'You can only review after the item has been returned.');
        }

        $alreadyReviewed = Review::where('borrow_request_id', $borrowRequest->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->route('dashboard')->with('info', 'You have already reviewed this borrowing.');
        }

        $isBorrower = ($borrowRequest->borrower_id === $user->id);
        $revieweeId = $isBorrower ? $borrowRequest->owner_id : $borrowRequest->borrower_id;

        Review::create([
            'borrow_request_id' => $borrowRequest->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('dashboard', ['tab' => 'returned'])
            ->with('success', 'Thank you! Your rating and review have been submitted.');
    }
}

