<?php

namespace App\Http\Controllers;

use App\Models\BorrowRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AccountStatusNotification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard with stats and overview.
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalItems = Item::count();
        $activeBorrows = BorrowRequest::where('status', 'approved')->count();
        $overdueItems = BorrowRequest::where('status', 'overdue')->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $pendingVerifications = User::where('is_verified', false)->whereNotNull('student_id')->count();

        $recentVerifications = User::where('is_verified', false)
            ->whereNotNull('student_id')
            ->latest()
            ->take(5)
            ->get();

        $recentReports = Report::with(['reporter', 'reportedUser', 'item'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentBorrows = BorrowRequest::with(['item', 'borrower', 'owner'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalItems',
            'activeBorrows',
            'overdueItems',
            'pendingReports',
            'pendingVerifications',
            'recentVerifications',
            'recentReports',
            'recentBorrows'
        ));
    }

    /**
     * List and search users with report counts.
     */
    public function users(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));
        $verified = trim($request->input('verified', ''));

        $query = User::withCount('reportsReceived')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($verified === 'verified') {
            $query->where('is_verified', true);
        } elseif ($verified === 'pending') {
            $query->where('is_verified', false);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users', compact('users', 'search', 'status', 'verified'));
    }

    /**
     * Approve student ID verification.
     */
    public function approveVerification(User $user)
    {
        $user->is_verified = true;
        $user->save();

        if (class_exists(AccountStatusNotification::class)) {
            $user->notify(new AccountStatusNotification('verified'));
        }

        return back()->with('success', "Student verification approved for {$user->name} ({$user->student_id}).");
    }

    /**
     * Suspend a user.
     */
    public function suspendUser(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend an administrator account.');
        }

        $reason = $request->input('reason', 'Violation of campus sharing safety terms.');

        $user->status = 'suspended';
        $user->save();

        if (class_exists(AccountStatusNotification::class)) {
            $user->notify(new AccountStatusNotification('suspended', $reason));
        }

        return back()->with('success', "User {$user->name} has been suspended.");
    }

    /**
     * Reactivate a suspended user.
     */
    public function reactivateUser(User $user)
    {
        $user->status = 'active';
        $user->save();

        if (class_exists(AccountStatusNotification::class)) {
            $user->notify(new AccountStatusNotification('reactivated'));
        }

        return back()->with('success', "User {$user->name} has been reactivated.");
    }

    /**
     * List and manage items.
     */
    public function items(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));
        $category = trim($request->input('category', ''));

        $query = Item::with(['user', 'categoryRelation'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($category !== '' && $category !== 'all') {
            $query->where('category', 'like', "%{$category}%");
        }

        $items = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.items', compact('items', 'categories', 'search', 'status', 'category'));
    }

    /**
     * Remove an item listing.
     */
    public function destroyItem(Item $item)
    {
        $title = $item->item_name;
        $item->delete();

        return back()->with('success', "Item '{$title}' was successfully removed by administrator.");
    }

    /**
     * Monitor borrowing activity.
     */
    public function borrowings(Request $request)
    {
        $status = trim($request->input('status', 'all'));

        $query = BorrowRequest::with(['item', 'borrower', 'owner'])->latest();

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        $borrowings = $query->paginate(15)->withQueryString();

        return view('admin.borrowings', compact('borrowings', 'status'));
    }

    /**
     * Review reports and take actions.
     */
    public function reports(Request $request)
    {
        $status = trim($request->input('status', 'pending'));

        $query = Report::with(['reporter', 'reportedUser', 'item'])->latest();

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('admin.reports', compact('reports', 'status'));
    }

    /**
     * Update report status or execute action.
     */
    public function updateReport(Request $request, Report $report)
    {
        $action = $request->input('action'); // 'reviewed', 'dismiss', 'suspend_user', 'delete_item'

        if ($action === 'dismiss') {
            $report->update(['status' => 'dismissed']);
            return back()->with('info', 'Report has been dismissed.');
        }

        if ($action === 'reviewed') {
            $report->update(['status' => 'reviewed']);
            return back()->with('success', 'Report marked as reviewed.');
        }

        if ($action === 'suspend_user' && $report->reportedUser) {
            $report->reportedUser->update(['status' => 'suspended']);
            if (class_exists(AccountStatusNotification::class)) {
                $report->reportedUser->notify(new AccountStatusNotification('suspended', 'Safety report investigation'));
            }
            $report->update(['status' => 'action_taken']);
            return back()->with('success', "Report action taken: User {$report->reportedUser->name} was suspended.");
        }

        if ($action === 'delete_item' && $report->item) {
            $report->item->delete();
            $report->update(['status' => 'action_taken']);
            return back()->with('success', 'Report action taken: Inappropriate item listing was deleted.');
        }

        return back()->with('error', 'Invalid action.');
    }
}

