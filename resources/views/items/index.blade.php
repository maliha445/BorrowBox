@extends('layouts.app')

@section('title', 'Browse Items - BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <h1 class="page-title">Browse Campus Items</h1>
            <p class="page-subtitle">Find books, lab gear, notes, electronics and more shared by students</p>
        </div>
        <div>
            <a href="{{ route('items.create') }}" class="btn">+ List an Item</a>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <form action="{{ route('items.index') }}" method="GET" style="background: #f8faf9; border: 1px solid #e5ebe7; border-radius: 12px; padding: 18px; margin-bottom: 26px;">
        <div style="display: grid; grid-template-columns: 2fr 1.2fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="margin-top: 0; font-size: 13px;">Search Items</label>
                <input 
                    type="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search by title, description or owner..."
                >
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="margin-top: 0; font-size: 13px;">Availability</label>
                <select name="status">
                    <option value="">Any Status</option>
                    <option value="available" {{ $status == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="borrowed" {{ $status == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn" style="padding: 11px 18px;">
                    Filter
                </button>
            </div>
        </div>

        @if ($search || $category || $status)
            <div style="margin-top: 12px; font-size: 13px; color: #667085; display: flex; align-items: center; justify-content: space-between;">
                <span>Active filters: 
                    @if ($search) <strong>"{{ $search }}"</strong> @endif
                    @if ($category) &bull; Category: <strong>{{ $category }}</strong> @endif
                    @if ($status) &bull; Status: <strong>{{ ucfirst($status) }}</strong> @endif
                </span>
                <a href="{{ route('items.index') }}" style="color: #27845a; font-weight: bold; text-decoration: underline;">Reset Filters</a>
            </div>
        @endif
    </form>

    <!-- Items Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @forelse ($items as $item)
            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
                <div style="background: #e8f2eb; height: 160px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                    @if ($item->image)
                        <img src="{{ asset($item->image) }}" alt="{{ $item->item_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        @php $cat = strtolower($item->category); @endphp
                        <span style="font-size: 55px;">
                            @if (str_contains($cat, 'book')) 📚
                            @elseif (str_contains($cat, 'calc')) 🧮
                            @elseif (str_contains($cat, 'electr')) 💻
                            @elseif (str_contains($cat, 'note') || str_contains($cat, 'lecture')) 📝
                            @elseif (str_contains($cat, 'lab')) 🔬
                            @elseif (str_contains($cat, 'sport')) ⚽
                            @elseif (str_contains($cat, 'station')) ✏️
                            @else 🎒
                            @endif
                        </span>
                    @endif

                    <!-- Status badge -->
                    <div style="position: absolute; top: 10px; right: 10px;">
                        @if ($item->status === 'available')
                            <span class="badge badge-available">● Available</span>
                        @elseif ($item->status === 'borrowed')
                            <span class="badge badge-borrowed">● Borrowed</span>
                        @elseif ($item->status === 'overdue')
                            <span class="badge badge-overdue">● Overdue</span>
                        @else
                            <span class="badge badge-returned">● {{ ucfirst($item->status) }}</span>
                        @endif
                    </div>
                </div>

                <div style="padding: 16px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h3 style="font-size: 17px; margin-bottom: 6px;">
                            <a href="{{ route('items.show', $item) }}" style="color: #253858; text-decoration: none;">
                                {{ $item->item_name }}
                            </a>
                        </h3>

                        <p style="color: #667085; font-size: 13px; line-height: 1.5; margin-bottom: 10px;">
                            {{ Str::limit($item->description, 80) }}
                        </p>

                        <div style="font-size: 12px; color: #55695f; margin-bottom: 6px;">
                            <strong>Category:</strong> {{ $item->category_display_name }}
                        </div>

                        <div style="font-size: 12px; color: #55695f; margin-bottom: 6px;">
                            <strong>Condition:</strong> {{ $item->condition ?? 'Good' }}
                        </div>

                        <div style="font-size: 12px; color: #667085; display: flex; align-items: center; gap: 4px;">
                            <span>Shared by:</span>
                            @if ($item->user)
                                <a href="{{ route('users.show', $item->user) }}" style="color: #27845a; font-weight: bold; text-decoration: none;">
                                    {{ $item->user->name }}
                                </a>
                                @if ($item->user->isVerified())
                                    <span class="badge badge-verified" style="font-size: 9px; padding: 1px 4px;">✓</span>
                                @endif
                            @else
                                <span>{{ $item->owner_name }}</span>
                            @endif
                        </div>
                    </div>

                    <div style="margin-top: 14px; pt: 12px; border-top: 1px solid #f0f4f2; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12px; color: #267653; font-weight: bold;">
                            🎁 +{{ $item->reward_coins }} coins
                        </span>
                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 40px 20px; text-align: center; color: #667085;">
                <h3 style="margin-bottom: 8px;">No items match your criteria.</h3>
                <p>Try searching for a different keyword or resetting your category filter.</p>
                <div style="margin-top: 16px;">
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Clear All Filters</a>
                </div>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $items->links() }}
    </div>
</div>
@endsection

