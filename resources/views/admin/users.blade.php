@extends('layouts.app')

@section('title', 'Manage Users - Admin | BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Admin Dashboard
            </a>
            <h1 class="page-title">Manage Students & Users</h1>
            <p class="page-subtitle">Verify student credentials, handle suspensions, and monitor community trust</p>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <form action="{{ route('admin.users') }}" method="GET" style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 16px; margin-bottom: 22px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="margin-top: 0; font-size: 13px;">Search Student</label>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by name, email or student ID...">
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Verification</label>
                <select name="verified">
                    <option value="">All</option>
                    <option value="pending" {{ $verified === 'pending' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="verified" {{ $verified === 'verified' ? 'selected' : '' }}>Verified Students</option>
                </select>
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Account Status</label>
                <select name="status">
                    <option value="">All</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn" style="padding: 11px 18px;">Filter</button>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
        @if ($users->isEmpty())
            <div style="text-align: center; padding: 40px; color: #667085;">
                <p>No students match the criteria.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>ID Card</th>
                            <th>Role</th>
                            <th>Verification</th>
                            <th>Status</th>
                            <th>Reports Received</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>
                                    <strong>{{ $u->name }}</strong><br>
                                    <small style="color: #667085;">{{ $u->email }}</small>
                                </td>
                                <td>{{ $u->student_id }}</td>
                                <td>
                                    @if ($u->id_card_image)
                                        <a href="{{ asset($u->id_card_image) }}" target="_blank" style="color: #27845a; font-weight: bold; font-size: 12px;">
                                            🔍 View Photo
                                        </a>
                                    @else
                                        <span style="color: #999; font-size: 12px;">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->isAdmin())
                                        <span class="badge badge-admin">Admin</span>
                                    @else
                                        <span style="font-size: 13px;">Student</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->isVerified())
                                        <span class="badge badge-verified">✓ Verified</span>
                                    @else
                                        <span class="badge badge-pending">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->isSuspended())
                                        <span class="badge badge-suspended">Suspended</span>
                                    @else
                                        <span class="badge badge-available">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->reports_received_count > 0)
                                        <span style="color: #c62828; font-weight: bold; font-size: 13px;">
                                            ⚠️ {{ $u->reports_received_count }} report(s)
                                        </span>
                                    @else
                                        <span style="color: #667085; font-size: 13px;">0</span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @if (!$u->isVerified())
                                        <form action="{{ route('admin.users.verify', $u) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Approve Student ID">Approve</button>
                                        </form>
                                    @endif

                                    @if (!$u->isAdmin())
                                        @if ($u->isSuspended())
                                            <form action="{{ route('admin.users.reactivate', $u) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline">Reactivate</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.suspend', $u) }}" method="POST" style="display:inline;" onsubmit="return confirm('Suspend user {{ $u->name }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" style="color: #c62828;">Suspend</button>
                                            </form>
                                        @endif
                                    @endif

                                    <a href="{{ route('users.show', $u) }}" class="btn btn-sm btn-secondary">Profile</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

