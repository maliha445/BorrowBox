<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Item;
use App\Notifications\NewBorrowRequestNotification;
use App\Notifications\BorrowRequestStatusNotification;
use App\Notifications\ReturnConfirmedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends Controller
{
    /**
     * Submit a new borrowing request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isVerified()) {
            return back()->with('error', 'Only verified student accounts can request to borrow items.');
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'start_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after_or_equal:start_date',
            'message' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Check if user is the owner
        if ($item->user_id && $item->user_id === $user->id) {
            return back()->with('error', 'You cannot borrow your own item.');
        }

        // Check if item is currently available
        if (!$item->isAvailable()) {
            return back()->with('error', 'This item is not currently available for borrowing.');
        }

        // Check for existing pending request from the same borrower
        $hasPending = BorrowRequest::where('item_id', $item->id)
            ->where('borrower_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending borrow request for this item.');
        }

        // Check for overlapping approved requests
        $hasOverlap = BorrowRequest::where('item_id', $item->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['due_date']])
                  ->orWhereBetween('due_date', [$validated['start_date'], $validated['due_date']])
                  ->orWhere(function ($sub) use ($validated) {
                      $sub->where('start_date', '<=', $validated['start_date'])
                          ->where('due_date', '>=', $validated['due_date']);
                  });
            })
            ->exists();

        if ($hasOverlap) {
            return back()->with('error', 'This item is already booked for the selected date range.');
        }

        $ownerId = $item->user_id ?: $user->id; // Fallback if no owner set

        $borrowRequest = BorrowRequest::create([
            'item_id' => $item->id,
            'borrower_id' => $user->id,
            'owner_id' => $ownerId,
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        // Send notification to owner if notification class exists
        if (class_exists(NewBorrowRequestNotification::class) && $borrowRequest->owner) {
            $borrowRequest->owner->notify(new NewBorrowRequestNotification($borrowRequest));
        }

        return redirect()->route('dashboard', ['tab' => 'outgoing'])
            ->with('success', 'Your borrow request has been sent to ' . ($item->owner_name ?: 'the owner') . '!');
    }

    /**
     * Owner approves a request.
     */
    public function approve(BorrowRequest $borrowRequest)
    {
        $user = Auth::user();

        if ($borrowRequest->owner_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($borrowRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $borrowRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $borrowRequest->item->update([
            'status' => 'borrowed',
            'is_available' => false,
        ]);

        // Reject other pending requests that overlap or are now conflicting
        BorrowRequest::where('item_id', $borrowRequest->item_id)
            ->where('id', '!=', $borrowRequest->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        if (class_exists(BorrowRequestStatusNotification::class) && $borrowRequest->borrower) {
            $borrowRequest->borrower->notify(new BorrowRequestStatusNotification($borrowRequest, 'approved'));
        }

        return back()->with('success', 'Borrow request approved! The item is now marked as borrowed.');
    }

    /**
     * Owner rejects a request.
     */
    public function reject(BorrowRequest $borrowRequest)
    {
        $user = Auth::user();

        if ($borrowRequest->owner_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($borrowRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $borrowRequest->update([
            'status' => 'rejected',
        ]);

        if (class_exists(BorrowRequestStatusNotification::class) && $borrowRequest->borrower) {
            $borrowRequest->borrower->notify(new BorrowRequestStatusNotification($borrowRequest, 'rejected'));
        }

        return back()->with('info', 'Borrow request was rejected.');
    }

    /**
     * Borrower cancels their own pending request.
     */
    public function cancel(BorrowRequest $borrowRequest)
    {
        $user = Auth::user();

        if ($borrowRequest->borrower_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($borrowRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $borrowRequest->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Borrow request cancelled.');
    }

    /**
     * Owner confirms item return.
     */
    public function returnConfirm(BorrowRequest $borrowRequest)
    {
        $user = Auth::user();

        if ($borrowRequest->owner_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($borrowRequest->status, ['approved', 'overdue'])) {
            return back()->with('error', 'Only approved or overdue items can be confirmed as returned.');
        }

        $borrowRequest->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        $borrowRequest->item->update([
            'status' => 'available',
            'is_available' => true,
        ]);

        if (class_exists(ReturnConfirmedNotification::class) && $borrowRequest->borrower) {
            $borrowRequest->borrower->notify(new ReturnConfirmedNotification($borrowRequest));
        }

        return back()->with('success', 'Item return confirmed! You can now rate and review each other.');
    }
}

