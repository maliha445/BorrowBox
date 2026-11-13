<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Show report form.
     */
    public function create(Request $request)
    {
        $type = $request->query('type'); // 'user' or 'item'
        $id = $request->query('id');

        $user = Auth::user();
        $targetUser = null;
        $targetItem = null;

        if ($type === 'user') {
            $targetUser = User::findOrFail($id);
            if ($targetUser->id === $user->id) {
                return back()->with('error', 'You cannot report your own account.');
            }

            $alreadyPending = Report::where('reporter_id', $user->id)
                ->where('reported_user_id', $targetUser->id)
                ->where('status', 'pending')
                ->exists();

            if ($alreadyPending) {
                return back()->with('info', 'You already have a pending report for this user under review by an administrator.');
            }
        } elseif ($type === 'item') {
            $targetItem = Item::findOrFail($id);
            if ($targetItem->user_id === $user->id) {
                return back()->with('error', 'You cannot report your own item listing.');
            }

            $alreadyPending = Report::where('reporter_id', $user->id)
                ->where('item_id', $targetItem->id)
                ->where('status', 'pending')
                ->exists();

            if ($alreadyPending) {
                return back()->with('info', 'You already have a pending report for this item listing under review by an administrator.');
            }
        } else {
            return redirect()->route('home')->with('error', 'Invalid report request.');
        }

        return view('reports.create', compact('type', 'targetUser', 'targetItem'));
    }

    /**
     * Store report in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'nullable|exists:users,id',
            'item_id' => 'nullable|exists:items,id',
            'reason' => 'required|string|max:255',
            'details' => 'required|string|max:2000',
        ]);

        if (empty($validated['reported_user_id']) && empty($validated['item_id'])) {
            return back()->with('error', 'A valid report target is required.');
        }

        $user = Auth::user();

        // Check for pending duplicate report
        $query = Report::where('reporter_id', $user->id)->where('status', 'pending');
        if (!empty($validated['reported_user_id'])) {
            $query->where('reported_user_id', $validated['reported_user_id']);
        }
        if (!empty($validated['item_id'])) {
            $query->where('item_id', $validated['item_id']);
        }

        if ($query->exists()) {
            return back()->with('info', 'You already have an active pending report for this target.');
        }

        Report::create([
            'reporter_id' => $user->id,
            'reported_user_id' => $validated['reported_user_id'] ?? null,
            'item_id' => $validated['item_id'] ?? null,
            'reason' => $validated['reason'],
            'details' => $validated['details'],
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Your safety report has been submitted to the campus administrator team.');
    }
}

