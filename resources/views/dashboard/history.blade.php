@extends('layouts.app')

@section('title', 'Borrowing History - BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <a href="{{ route('dashboard') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Dashboard
            </a>
            <h1 class="page-title">Borrowing & Sharing History</h1>
            <p class="page-subtitle">Complete chronological record of all items you have borrowed or lent on campus</p>
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="{{ route('history', ['filter' => 'all']) }}" class="btn btn-sm {{ $filter === 'all' ? '' : 'btn-secondary' }}">
                All Activity
            </a>
            <a href="{{ route('history', ['filter' => 'borrowed']) }}" class="btn btn-sm {{ $filter === 'borrowed' ? '' : 'btn-secondary' }}">
                Borrowed by Me
            </a>
            <a href="{{ route('history', ['filter' => 'shared']) }}" class="btn btn-sm {{ $filter === 'shared' ? '' : 'btn-secondary' }}">
                Shared by Me
            </a>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
        @if ($records->isEmpty())
            <div style="text-align: center; padding: 40px; color: #667085;">
                <p>No activity records found matching this filter.</p>
                <div style="margin-top: 15px;">
                    <a href="{{ route('items.index') }}" class="btn btn-sm">Explore Items to Borrow</a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>My Role</th>
                            <th>Other Student</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Returned Date</th>
                            <th>Status</th>
                            <th style="text-align: right;">Review / Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $req)
                            @php
                                $isBorrower = ($req->borrower_id === $user->id);
                                $otherStudent = $isBorrower ? $req->owner : $req->borrower;
                                $hasReviewed = $req->hasReviewBy($user->id);
                            @endphp
                            <tr>
                                <td>
                                    @if ($req->item)
                                        <a href="{{ route('items.show', $req->item) }}" style="font-weight: bold; color: #27845a; text-decoration: none;">
                                            {{ $req->item->item_name }}
                                        </a>
                                    @else
                                        <em>Deleted Item</em>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $isBorrower ? 'badge-borrowed' : 'badge-available' }}">
                                        {{ $isBorrower ? 'Borrower' : 'Lender / Owner' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($otherStudent)
                                        <a href="{{ route('users.show', $otherStudent) }}" style="color: #34483d; text-decoration: none; font-weight: 500;">
                                            {{ $otherStudent->name }}
                                        </a>
                                        @if ($otherStudent->isVerified())
                                            <span class="badge badge-verified" style="font-size: 9px; padding: 1px 4px;">✓</span>
                                        @endif
                                    @else
                                        <em>Unknown</em>
                                    @endif
                                </td>
                                <td>{{ $req->start_date->format('M d, Y') }}</td>
                                <td>{{ $req->due_date->format('M d, Y') }}</td>
                                <td>
                                    {{ $req->returned_at ? $req->returned_at->format('M d, Y') : '-' }}
                                </td>
                                <td>
                                    @if ($req->status === 'returned')
                                        <span class="badge badge-available">Returned</span>
                                    @elseif ($req->status === 'approved')
                                        <span class="badge badge-borrowed">Active Borrow</span>
                                    @elseif ($req->status === 'overdue')
                                        <span class="badge badge-overdue">Overdue</span>
                                    @elseif ($req->status === 'pending')
                                        <span class="badge badge-pending">Pending</span>
                                    @elseif ($req->status === 'rejected')
                                        <span class="badge badge-suspended">Rejected</span>
                                    @elseif ($req->status === 'cancelled')
                                        <span class="badge badge-returned">Cancelled</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if ($req->status === 'returned')
                                        @if ($hasReviewed)
                                            <span style="color: #27845a; font-size: 13px; font-weight: bold;">★ Reviewed</span>
                                        @elseif ($otherStudent)
                                            <a href="{{ route('reviews.create', ['borrow_request_id' => $req->id]) }}" class="btn btn-sm btn-outline">
                                                Review
                                            </a>
                                        @endif
                                    @elseif ($req->item)
                                        <a href="{{ route('items.show', $req->item) }}" class="btn btn-sm btn-outline">View Item</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

