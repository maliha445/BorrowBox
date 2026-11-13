@extends('layouts.app')

@section('title', 'Admin Dashboard - BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <h1 class="page-title">🛡️ Admin Control Panel</h1>
            <p class="page-subtitle">Campus sharing oversight, student verifications, and trust management</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-secondary">Manage Users</a>
            <a href="{{ route('admin.items') }}" class="btn btn-sm btn-secondary">Manage Items</a>
            <a href="{{ route('admin.borrowings') }}" class="btn btn-sm btn-secondary">Borrowings</a>
            <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-secondary" style="color: #c62828;">Reports ({{ $pendingReports }})</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid-3" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Registered Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #27845a;">{{ $totalItems }}</div>
            <div class="stat-label">Total Shared Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #1a73e8;">{{ $activeBorrows }}</div>
            <div class="stat-label">Active Borrows on Campus</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="{{ $overdueItems > 0 ? 'color: #d93025;' : '' }}">{{ $overdueItems }}</div>
            <div class="stat-label">Overdue Borrowings</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="{{ $pendingVerifications > 0 ? 'color: #b76e00;' : '' }}">{{ $pendingVerifications }}</div>
            <div class="stat-label">Pending Student Verifications</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="{{ $pendingReports > 0 ? 'color: #c62828;' : '' }}">{{ $pendingReports }}</div>
            <div class="stat-label">Pending Safety Reports</div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Pending Verifications Section -->
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h3 style="font-size: 17px; color: #253858;">Pending Student Verifications</h3>
                <a href="{{ route('admin.users', ['verified' => 'pending']) }}" style="font-size: 13px; color: #27845a; font-weight: bold; text-decoration: none;">View All</a>
            </div>

            @if ($recentVerifications->isEmpty())
                <p style="color: #667085; font-size: 14px; text-align: center; padding: 25px;">No students awaiting verification right now.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>ID Card</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentVerifications as $u)
                                <tr>
                                    <td>
                                        <strong>{{ $u->name }}</strong><br>
                                        <small style="color: #667085;">{{ $u->email }}</small>
                                    </td>
                                    <td>{{ $u->student_id }}</td>
                                    <td>
                                        @if ($u->id_card_image)
                                            <a href="{{ asset($u->id_card_image) }}" target="_blank" style="color: #27845a; font-weight: bold; font-size: 12px;">
                                                🔍 View Card
                                            </a>
                                        @else
                                            <span style="color: #999; font-size: 12px;">No photo</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('admin.users.verify', $u) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Pending Safety Reports Section -->
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h3 style="font-size: 17px; color: #c62828;">Pending Safety Reports</h3>
                <a href="{{ route('admin.reports') }}" style="font-size: 13px; color: #c62828; font-weight: bold; text-decoration: none;">View All</a>
            </div>

            @if ($recentReports->isEmpty())
                <p style="color: #667085; font-size: 14px; text-align: center; padding: 25px;">No unresolved reports at the moment.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Target</th>
                                <th>Reason</th>
                                <th>Reporter</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentReports as $rep)
                                <tr>
                                    <td>
                                        @if ($rep->reportedUser)
                                            <strong>👤 {{ $rep->reportedUser->name }}</strong>
                                        @elseif ($rep->item)
                                            <strong>📦 {{ $rep->item->item_name }}</strong>
                                        @endif
                                    </td>
                                    <td style="font-size: 13px;">{{ $rep->reason }}</td>
                                    <td style="font-size: 12px; color: #667085;">{{ $rep->reporter?->name ?? 'Student' }}</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-outline">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Campus Borrowing Activity -->
    <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px; margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h3 style="font-size: 17px; color: #253858;">Recent Campus Borrowing Transactions</h3>
            <a href="{{ route('admin.borrowings') }}" style="font-size: 13px; color: #27845a; font-weight: bold; text-decoration: none;">View All Borrowings</a>
        </div>

        @if ($recentBorrows->isEmpty())
            <p style="color: #667085; font-size: 14px; text-align: center; padding: 20px;">No borrow requests made yet.</p>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Borrower</th>
                            <th>Owner</th>
                            <th>Dates</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBorrows as $b)
                            <tr>
                                <td>
                                    <a href="{{ route('items.show', $b->item) }}" style="color: #27845a; font-weight: bold; text-decoration: none;">
                                        {{ $b->item->item_name }}
                                    </a>
                                </td>
                                <td>{{ $b->borrower?->name ?? 'Student' }}</td>
                                <td>{{ $b->owner?->name ?? 'Owner' }}</td>
                                <td>{{ $b->start_date->format('M d') }} &rarr; {{ $b->due_date->format('M d, Y') }}</td>
                                <td>
                                    @if ($b->status === 'approved')
                                        <span class="badge badge-borrowed">Approved</span>
                                    @elseif ($b->status === 'returned')
                                        <span class="badge badge-available">Returned</span>
                                    @elseif ($b->status === 'overdue')
                                        <span class="badge badge-overdue">Overdue</span>
                                    @elseif ($b->status === 'pending')
                                        <span class="badge badge-pending">Pending</span>
                                    @else
                                        <span class="badge badge-returned">{{ ucfirst($b->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

