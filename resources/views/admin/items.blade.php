@extends('layouts.app')

@section('title', 'Manage Items - Admin | BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Admin Dashboard
            </a>
            <h1 class="page-title">Manage Listed Items</h1>
            <p class="page-subtitle">Inspect campus listings and remove inappropriate or prohibited items</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <form action="{{ route('admin.items') }}" method="GET" style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 16px; margin-bottom: 22px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="margin-top: 0; font-size: 13px;">Search Items</label>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search by item title or owner...">
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}" {{ $category === $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="available" {{ $status === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="borrowed" {{ $status === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="overdue" {{ $status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn" style="padding: 11px 18px;">Filter</button>
            </div>
        </div>
    </form>

    <!-- Items Table -->
    <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px;">
        @if ($items->isEmpty())
            <div style="text-align: center; padding: 40px; color: #667085;">
                <p>No items found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Listed Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
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
                                <td>
                                    @if ($item->user)
                                        <a href="{{ route('users.show', $item->user) }}" style="color: #34483d; text-decoration: none;">
                                            {{ $item->user->name }}
                                        </a>
                                    @else
                                        {{ $item->owner_name }}
                                    @endif
                                </td>
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
                                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove listing &quot;{{ $item->item_name }}&quot; permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

