@extends('layouts.app')

@section('title', 'Dashboard - BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <h1 class="page-title">Student Dashboard</h1>
            <p class="page-subtitle">Welcome back, {{ $user->name }}! Manage your shared campus resources and borrowings</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('items.create') }}" class="btn">+ List New Item</a>
            <a href="{{ route('history') }}" class="btn btn-secondary">Full History</a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid-4" style="margin-bottom: 26px;">
        <div class="stat-card">
            <div class="stat-value">{{ $myItemsCount }}</div>
            <div class="stat-label">My Listed Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #1a73e8;">{{ $borrowedItemsCount }}</div>
            <div class="stat-label">Currently Borrowed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #b76e00;">{{ $incomingCount }}</div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="{{ $overdueCount > 0 ? 'color: #d93025;' : 'color: #27845a;' }}">
                {{ $overdueCount }}
            </div>
            <div class="stat-label">{{ $overdueCount > 0 ? '⚠️ Overdue Items' : 'Overdue Items' }}</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs">
        <a href="{{ route('dashboard', ['tab' => 'my-items']) }}" class="tab {{ $tab === 'my-items' ? 'active' : '' }}">
            📦 My Items ({{ $myItemsCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'borrowed']) }}" class="tab {{ $tab === 'borrowed' ? 'active' : '' }}">
            📥 Borrowed Items ({{ $borrowedItemsCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'incoming']) }}" class="tab {{ $tab === 'incoming' ? 'active' : '' }}">
            📬 Incoming Requests ({{ $incomingCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'outgoing']) }}" class="tab {{ $tab === 'outgoing' ? 'active' : '' }}">
            📤 Outgoing Requests ({{ $outgoingCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'shared']) }}" class="tab {{ $tab === 'shared' ? 'active' : '' }}">
            🤝 Currently Shared ({{ $sharedCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'returned']) }}" class="tab {{ $tab === 'returned' ? 'active' : '' }}">
            ✅ Returned ({{ $returnedCount }})
        </a>
        <a href="{{ route('dashboard', ['tab' => 'overdue']) }}" class="tab {{ $tab === 'overdue' ? 'active' : '' }}">
            ⏰ Overdue ({{ $overdueCount }})
        </a>
    </div>

    <!-- TAB CONTENTS -->

    {{-- TAB: MY ITEMS --}}
    @if ($tab === 'my-items')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 18px; color: #253858;">My Listed Items</h3>
                <a href="{{ route('items.create') }}" class="btn btn-sm">+ Add Item</a>
            </div>

            @if ($myItems->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>You haven't listed any items for campus sharing yet.</p>
                    <a href="{{ route('items.create') }}" class="btn btn-sm" style="margin-top: 12px;">Share an Item &bull; Earn 100 Coins</a>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Listed On</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myItems as $item)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            @if ($item->image)
                                                <img src="{{ asset($item->image) }}" alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px;">
                                            @endif
                                            <div>
                                                <a href="{{ route('items.show', $item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                                    {{ $item->item_name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->category_display_name }}</td>
                                    <td>{{ $item->condition ?? 'Good' }}</td>
                                    <td>
                                        @if ($item->status === 'available')
                                            <span class="badge badge-available">Available</span>
                                        @elseif ($item->status === 'borrowed')
                                            <span class="badge badge-borrowed">Borrowed</span>
                                        @elseif ($item->status === 'overdue')
                                            <span class="badge badge-overdue">Overdue</span>
                                        @else
                                            <span class="badge badge-returned">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at->format('M d, Y') }}</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this listing?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: BORROWED ITEMS --}}
    @if ($tab === 'borrowed')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #253858; margin-bottom: 16px;">Items You Are Currently Borrowing</h3>

            @if ($borrowedItems->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>You have no active borrowings at this moment.</p>
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline" style="margin-top: 12px;">Browse Available Items</a>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Owner</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowedItems as $req)
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($req->owner)
                                            <a href="{{ route('users.show', $req->owner) }}" style="color: #34483d; text-decoration: none;">
                                                {{ $req->owner->name }}
                                                @if ($req->owner->isVerified()) <span class="badge badge-verified" style="font-size: 9px;">✓</span> @endif
                                            </a>
                                            <br><small style="color: #667085;">Phone: {{ $req->owner->phone ?? 'N/A' }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $req->start_date->format('M d, Y') }}</td>
                                    <td>
                                        <span style="{{ $req->due_date->isPast() ? 'color: #c62828; font-weight: bold;' : '' }}">
                                            {{ $req->due_date->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($req->status === 'overdue')
                                            <span class="badge badge-overdue">Overdue</span>
                                        @else
                                            <span class="badge badge-borrowed">Active Borrow</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('items.show', $req->item) }}" class="btn btn-sm btn-outline">View Item</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: INCOMING REQUESTS --}}
    @if ($tab === 'incoming')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #253858; margin-bottom: 16px;">Incoming Requests for Your Items</h3>

            @if ($incomingRequests->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>No incoming borrowing requests right now.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Requester</th>
                                <th>Requested Dates</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomingRequests as $req)
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($req->borrower)
                                            <a href="{{ route('users.show', $req->borrower) }}" style="color: #34483d; text-decoration: none; font-weight: 500;">
                                                {{ $req->borrower->name }}
                                                @if ($req->borrower->isVerified()) <span class="badge badge-verified" style="font-size: 9px;">✓</span> @endif
                                            </a>
                                            <br><small style="color: #667085;">ID: {{ $req->borrower->student_id }} &bull; Rating: ★ {{ number_format($req->borrower->averageRating(), 1) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $req->start_date->format('M d') }} &rarr; {{ $req->due_date->format('M d, Y') }}
                                    </td>
                                    <td style="max-width: 200px; color: #667085; font-size: 13px;">
                                        {{ $req->message ?: 'No message' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-pending">{{ ucfirst($req->status) }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if ($req->status === 'pending')
                                            <form action="{{ route('borrow-requests.approve', $req) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('borrow-requests.reject', $req) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" style="color: #c62828;">Reject</button>
                                            </form>
                                        @elseif (in_array($req->status, ['approved', 'overdue']))
                                            <form action="{{ route('borrow-requests.return', $req) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline">Confirm Return</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: OUTGOING REQUESTS --}}
    @if ($tab === 'outgoing')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #253858; margin-bottom: 16px;">Requests You Sent to Others</h3>

            @if ($outgoingRequests->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>You haven't sent any borrow requests yet.</p>
                    <a href="{{ route('items.index') }}" class="btn btn-sm" style="margin-top: 12px;">Browse Items</a>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Owner</th>
                                <th>Requested Dates</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($outgoingRequests as $req)
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($req->owner)
                                            <a href="{{ route('users.show', $req->owner) }}" style="color: #34483d; text-decoration: none;">
                                                {{ $req->owner->name }}
                                                @if ($req->owner->isVerified()) <span class="badge badge-verified" style="font-size: 9px;">✓</span> @endif
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $req->start_date->format('M d') }} &rarr; {{ $req->due_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @if ($req->status === 'pending')
                                            <span class="badge badge-pending">Pending Approval</span>
                                        @elseif ($req->status === 'approved')
                                            <span class="badge badge-borrowed">Approved</span>
                                        @elseif ($req->status === 'rejected')
                                            <span class="badge badge-suspended">Rejected</span>
                                        @elseif ($req->status === 'cancelled')
                                            <span class="badge badge-returned">Cancelled</span>
                                        @else
                                            <span class="badge badge-returned">{{ ucfirst($req->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $req->created_at->diffForHumans() }}</td>
                                    <td style="text-align: right;">
                                        @if ($req->status === 'pending')
                                            <form action="{{ route('borrow-requests.cancel', $req) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this borrow request?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" style="color: #c62828;">Cancel Request</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('items.show', $req->item) }}" class="btn btn-sm btn-outline">View Item</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: SHARED ITEMS --}}
    @if ($tab === 'shared')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #253858; margin-bottom: 16px;">Your Items Currently Lent Out to Students</h3>

            @if ($sharedItems->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>None of your items are currently borrowed by anyone.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Borrower</th>
                                <th>Borrowed Since</th>
                                <th>Return Due</th>
                                <th>Status</th>
                                <th style="text-align: right;">Confirm Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sharedItems as $req)
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($req->borrower)
                                            <a href="{{ route('users.show', $req->borrower) }}" style="color: #34483d; text-decoration: none;">
                                                {{ $req->borrower->name }}
                                            </a>
                                            <br><small style="color: #667085;">Phone: {{ $req->borrower->phone ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $req->start_date->format('M d, Y') }}</td>
                                    <td>{{ $req->due_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-borrowed">Lent Out</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('borrow-requests.return', $req) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm">Confirm Return</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: RETURNED ITEMS --}}
    @if ($tab === 'returned')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #253858; margin-bottom: 16px;">Completed & Returned Borrowings</h3>

            @if ($returnedItems->isEmpty())
                <div style="text-align: center; padding: 40px; color: #667085;">
                    <p>No completed borrowing cycles yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Role</th>
                                <th>Other Student</th>
                                <th>Returned On</th>
                                <th>Review</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnedItems as $req)
                                @php
                                    $isBorrower = ($req->borrower_id === $user->id);
                                    $otherStudent = $isBorrower ? $req->owner : $req->borrower;
                                    $hasReviewed = $req->hasReviewBy($user->id);
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $isBorrower ? 'badge-borrowed' : 'badge-available' }}">
                                            {{ $isBorrower ? 'Borrower' : 'Owner' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($otherStudent)
                                            <a href="{{ route('users.show', $otherStudent) }}" style="color: #34483d; text-decoration: none;">
                                                {{ $otherStudent->name }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $req->returned_at ? $req->returned_at->format('M d, Y') : 'Returned' }}</td>
                                    <td>
                                        @if ($hasReviewed)
                                            <span style="color: #27845a; font-weight: bold; font-size: 12px;">✓ Reviewed</span>
                                        @else
                                            <span style="color: #b76e00; font-size: 12px;">Pending Review</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if (!$hasReviewed && $otherStudent)
                                            <a href="{{ route('reviews.create', ['borrow_request_id' => $req->id]) }}" class="btn btn-sm btn-outline">
                                                Rate {{ $isBorrower ? 'Owner' : 'Borrower' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- TAB: OVERDUE ITEMS --}}
    @if ($tab === 'overdue')
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; color: #c62828; margin-bottom: 16px;">Overdue Items</h3>

            @if ($overdueItems->isEmpty())
                <div style="text-align: center; padding: 40px; color: #27845a;">
                    <p>🎉 Excellent! No overdue items on your account.</p>
                </div>
            @else
                <div class="alert alert-danger" style="margin-bottom: 18px;">
                    ⚠️ Please coordinate return for overdue items to maintain campus trust and avoid borrowing restrictions.
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Role</th>
                                <th>Other Party</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueItems as $req)
                                @php
                                    $isBorrower = ($req->borrower_id === $user->id);
                                    $otherStudent = $isBorrower ? $req->owner : $req->borrower;
                                    $daysPast = now()->diffInDays($req->due_date);
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #c62828; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    </td>
                                    <td>{{ $isBorrower ? 'Borrower' : 'Owner' }}</td>
                                    <td>
                                        @if ($otherStudent)
                                            <a href="{{ route('users.show', $otherStudent) }}" style="color: #34483d; text-decoration: none;">
                                                {{ $otherStudent->name }}
                                            </a>
                                            <br><small style="color: #667085;">Phone: {{ $otherStudent->phone ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td style="color: #c62828; font-weight: bold;">
                                        {{ $req->due_date->format('M d, Y') }}
                                    </td>
                                    <td style="color: #c62828; font-weight: bold;">
                                        {{ $daysPast }} day(s) overdue
                                    </td>
                                    <td style="text-align: right;">
                                        @if (!$isBorrower)
                                            <form action="{{ route('borrow-requests.return', $req) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm">Confirm Return</button>
                                            </form>
                                        @else
                                            <span style="font-size: 13px; color: #c62828;">Return to owner ASAP</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

