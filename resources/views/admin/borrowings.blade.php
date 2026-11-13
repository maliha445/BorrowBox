@extends('layouts.app')

@section('title', 'Monitor Borrowings - Admin | BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Admin Dashboard
            </a>
            <h1 class="page-title">Monitor Campus Borrowing Activity</h1>
            <p class="page-subtitle">Track all borrowing exchanges, return dates, and overdue items across campus</p>
        </div>

        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <a href="{{ route('admin.borrowings', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? '' : 'btn-secondary' }}">All</a>
            <a href="{{ route('admin.borrowings', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? '' : 'btn-secondary' }}">Pending</a>
            <a href="{{ route('admin.borrowings', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? '' : 'btn-secondary' }}">Approved</a>
            <a href="{{ route('admin.borrowings', ['status' => 'overdue']) }}" class="btn btn-sm {{ $status === 'overdue' ? 'btn-danger' : 'btn-secondary' }}">Overdue</a>
            <a href="{{ route('admin.borrowings', ['status' => 'returned']) }}" class="btn btn-sm {{ $status === 'returned' ? '' : 'btn-secondary' }}">Returned</a>
            <a href="{{ route('admin.borrowings', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? '' : 'btn-secondary' }}">Rejected</a>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
        @if ($borrowings->isEmpty())
            <div style="text-align: center; padding: 40px; color: #667085;">
                <p>No borrow requests found matching this status.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Borrower</th>
                            <th>Owner</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Returned At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowings as $req)
                            <tr>
                                <td>#{{ $req->id }}</td>
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
                                    @if ($req->borrower)
                                        <a href="{{ route('users.show', $req->borrower) }}" style="color: #34483d; text-decoration: none;">
                                            {{ $req->borrower->name }}
                                        </a>
                                        <br><small style="color: #667085;">ID: {{ $req->borrower->student_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($req->owner)
                                        <a href="{{ route('users.show', $req->owner) }}" style="color: #34483d; text-decoration: none;">
                                            {{ $req->owner->name }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $req->start_date->format('M d, Y') }}</td>
                                <td>{{ $req->due_date->format('M d, Y') }}</td>
                                <td>{{ $req->returned_at ? $req->returned_at->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if ($req->status === 'approved')
                                        <span class="badge badge-borrowed">Approved</span>
                                    @elseif ($req->status === 'returned')
                                        <span class="badge badge-available">Returned</span>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

