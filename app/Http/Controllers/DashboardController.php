<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * User dashboard with sections and counts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'my-items');

        // Stats counts
        $myItemsCount = Item::where('user_id', $user->id)->count();

        $borrowedItemsCount = BorrowRequest::where('borrower_id', $user->id)
            ->whereIn('status', ['approved', 'overdue'])
            ->count();

        $incomingCount = BorrowRequest::where('owner_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $outgoingCount = BorrowRequest::where('borrower_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $sharedCount = BorrowRequest::where('owner_id', $user->id)
            ->whereIn('status', ['approved'])
            ->count();

        $overdueCount = BorrowRequest::where(function ($q) use ($user) {
            $q->where('borrower_id', $user->id)
              ->orWhere('owner_id', $user->id);
        })->where('status', 'overdue')->count();

        $returnedCount = BorrowRequest::where(function ($q) use ($user) {
            $q->where('borrower_id', $user->id)
              ->orWhere('owner_id', $user->id);
        })->where('status', 'returned')->count();

        // Data for current tab
        $myItems = Item::where('user_id', $user->id)->with('categoryRelation')->latest()->get();

        $borrowedItems = BorrowRequest::where('borrower_id', $user->id)
            ->whereIn('status', ['approved', 'overdue'])
            ->with(['item', 'owner'])
            ->latest()
            ->get();

        $incomingRequests = BorrowRequest::where('owner_id', $user->id)
            ->with(['item', 'borrower'])
            ->latest()
            ->get();

        $outgoingRequests = BorrowRequest::where('borrower_id', $user->id)
            ->with(['item', 'owner'])
            ->latest()
            ->get();

        $sharedItems = BorrowRequest::where('owner_id', $user->id)
            ->where('status', 'approved')
            ->with(['item', 'borrower'])
            ->latest()
            ->get();

        $overdueItems = BorrowRequest::where(function ($q) use ($user) {
            $q->where('borrower_id', $user->id)
              ->orWhere('owner_id', $user->id);
        })->where('status', 'overdue')
          ->with(['item', 'borrower', 'owner'])
          ->latest()
          ->get();

        $returnedItems = BorrowRequest::where(function ($q) use ($user) {
            $q->where('borrower_id', $user->id)
              ->orWhere('owner_id', $user->id);
        })->where('status', 'returned')
          ->with(['item', 'borrower', 'owner', 'reviews'])
          ->latest()
          ->get();

        return view('dashboard.index', compact(
            'user',
            'tab',
            'myItemsCount',
            'borrowedItemsCount',
            'incomingCount',
            'outgoingCount',
            'sharedCount',
            'overdueCount',
            'returnedCount',
            'myItems',
            'borrowedItems',
            'incomingRequests',
            'outgoingRequests',
            'sharedItems',
            'overdueItems',
            'returnedItems'
        ));
    }

    /**
     * History page listing all past borrowed and shared activity.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'all'); // all, borrowed, shared

        $query = BorrowRequest::with(['item', 'borrower', 'owner', 'reviews']);

        if ($filter === 'borrowed') {
            $query->where('borrower_id', $user->id);
        } elseif ($filter === 'shared') {
            $query->where('owner_id', $user->id);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('borrower_id', $user->id)
                  ->orWhere('owner_id', $user->id);
            });
        }

        $records = $query->latest()->paginate(15)->withQueryString();

        return view('dashboard.history', compact('records', 'filter', 'user'));
    }
}

