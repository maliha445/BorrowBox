@extends('layouts.app')

@section('title', $item->item_name . ' - BorrowBox')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <a href="{{ route('items.index') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Browse Items
            </a>
            <h1 class="page-title">{{ $item->item_name }}</h1>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
            @if ($item->status === 'available')
                <span class="badge badge-available" style="font-size: 14px; padding: 6px 12px;">● Available to Borrow</span>
            @elseif ($item->status === 'borrowed')
                <span class="badge badge-borrowed" style="font-size: 14px; padding: 6px 12px;">● Currently Borrowed</span>
            @elseif ($item->status === 'overdue')
                <span class="badge badge-overdue" style="font-size: 14px; padding: 6px 12px;">● Overdue</span>
            @else
                <span class="badge badge-returned" style="font-size: 14px; padding: 6px 12px;">● {{ ucfirst($item->status) }}</span>
            @endif

            @auth
                @if (auth()->id() === $item->user_id || auth()->user()->isAdmin())
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this listing?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    <div class="grid-2">
        <!-- Left Column: Item Image & Details -->
        <div>
            <div style="background: #e8f2eb; border-radius: 12px; height: 280px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #dce8e0; margin-bottom: 20px;">
                @if ($item->image)
                    <img src="{{ asset($item->image) }}" alt="{{ $item->item_name }}" style="width: 100%; height: 100%; object-fit: contain; background: white;">
                @else
                    @php $cat = strtolower($item->category); @endphp
                    <span style="font-size: 80px;">
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
            </div>

            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; margin-bottom: 12px; color: #253858;">About this Item</h3>
                <p style="font-size: 14px; color: #4a5568; line-height: 1.7; white-space: pre-line;">{{ $item->description }}</p>

                <div style="margin-top: 18px; pt: 14px; border-top: 1px solid #eef2f0; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div>
                        <span style="color: #667085;">Category:</span><br>
                        <strong>{{ $item->category_display_name }}</strong>
                    </div>
                    <div>
                        <span style="color: #667085;">Condition:</span><br>
                        <strong>{{ $item->condition ?? 'Good' }}</strong>
                    </div>
                    <div>
                        <span style="color: #667085;">Listed:</span><br>
                        <strong>{{ $item->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div>
                        <span style="color: #667085;">Reward:</span><br>
                        <strong>{{ $item->reward_coins }} coins earned</strong>
                    </div>
                </div>
            </div>

            <!-- Item Reviews -->
            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 10px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <h3 style="font-size: 16px; color: #253858; margin: 0;">Item Borrow Reviews</h3>
                    <div style="color: #e59819; font-size: 14px; font-weight: bold;">
                        ★ {{ number_format($item->averageRating(), 1) }} ({{ $reviews->count() }})
                    </div>
                </div>

                @forelse ($reviews as $rev)
                    <div style="border-bottom: 1px solid #f0f0f0; padding: 10px 0; font-size: 13px;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong>{{ $rev->reviewer?->name ?? 'Student' }}</strong>
                            <span style="color: #e59819;">{{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}</span>
                        </div>
                        <p style="color: #667085; margin-top: 4px;">{{ $rev->comment }}</p>
                        <span style="font-size: 11px; color: #999;">{{ $rev->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p style="font-size: 13px; color: #888;">No borrowing reviews yet for this item.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Owner Profile & Request Box -->
        <div>
            <!-- Owner Profile Card -->
            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 20px; margin-bottom: 22px;">
                <h3 style="font-size: 16px; color: #253858; margin-bottom: 14px;">Item Owner</h3>

                @if ($item->user)
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: #e8f2eb; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #27845a; font-weight: bold;">
                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <a href="{{ route('users.show', $item->user) }}" style="font-weight: bold; font-size: 16px; color: #253858; text-decoration: none;">
                                    {{ $item->user->name }}
                                </a>
                                @if ($item->user->isVerified())
                                    <span class="badge badge-verified">✓ Verified</span>
                                @else
                                    <span class="badge badge-pending">Unverified</span>
                                @endif
                            </div>
                            <div style="font-size: 12px; color: #667085; margin-top: 2px;">
                                Student ID: {{ substr($item->user->student_id ?? '', 0, 4) . '***' }} &bull; Member since {{ $item->user->created_at->format('M Y') }}
                            </div>
                        </div>
                    </div>

                    <div style="background: #f8faf9; border-radius: 8px; padding: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <span>Owner Rating:</span>
                        <span style="color: #e59819; font-weight: bold;">
                            ★ {{ number_format($item->user->averageRating(), 1) }} / 5.0 ({{ $item->user->reviewsReceived->count() }} reviews)
                        </span>
                    </div>
                @else
                    <div style="font-weight: bold; font-size: 15px; color: #253858;">{{ $item->owner_name }}</div>
                @endif
            </div>

            <!-- Borrow Request Action Box -->
            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 22px;">
                <h3 style="font-size: 18px; color: #253858; margin-bottom: 12px;">Request to Borrow</h3>

                @guest
                    <div style="background: #f8faf9; border: 1px solid #e5ebe7; border-radius: 8px; padding: 18px; text-align: center;">
                        <p style="font-size: 14px; color: #667085; margin-bottom: 15px;">
                            You need a verified campus account to send a borrow request.
                        </p>
                        <a href="{{ route('login') }}" class="btn" style="width: 100%;">Log In to Request</a>
                        <div style="margin-top: 10px; font-size: 13px;">
                            New student? <a href="{{ route('register') }}" style="color: #27845a; font-weight: bold;">Register</a>
                        </div>
                    </div>
                @else
                    @if (auth()->id() === $item->user_id)
                        <div style="background: #eef6ff; border: 1px solid #bee0ff; border-radius: 8px; padding: 16px; color: #155799; font-size: 14px;">
                            ℹ️ <strong>This is your listing.</strong> You can update the item details or mark its availability.
                            <div style="margin-top: 12px;">
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm">Edit Listing</a>
                            </div>
                        </div>
                    @elseif (!auth()->user()->isVerified())
                        <div style="background: #fff4e5; border: 1px solid #ffdca8; border-radius: 8px; padding: 16px; color: #b76e00; font-size: 14px; line-height: 1.5;">
                            ⚠️ <strong>Verification Required:</strong> Your account is not yet verified by the administrator. Once verified, you will be able to request items.
                            <div style="margin-top: 12px;">
                                <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline">Check Profile & Submit ID</a>
                            </div>
                        </div>
                    @elseif (!$item->isAvailable())
                        <div style="background: #fff0f0; border: 1px solid #ffd4d4; border-radius: 8px; padding: 16px; color: #c62828; font-size: 14px;">
                            🚫 <strong>Not Currently Available:</strong> This item is currently marked as {{ $item->status }}. You can check back later when it is returned.
                        </div>
                    @else
                        <!-- Active Request Form -->
                        <form action="{{ route('borrow-requests.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">

                            <label for="start_date">Borrow Date (Start)</label>
                            <input 
                                type="date" 
                                id="start_date" 
                                name="start_date" 
                                value="{{ old('start_date', date('Y-m-d')) }}" 
                                min="{{ date('Y-m-d') }}" 
                                required
                            >

                            <label for="due_date">Return Date (Due)</label>
                            <input 
                                type="date" 
                                id="due_date" 
                                name="due_date" 
                                value="{{ old('due_date', date('Y-m-d', strtotime('+3 days'))) }}" 
                                min="{{ date('Y-m-d') }}" 
                                required
                            >

                            <label for="message">Message to Owner (optional)</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="3" 
                                placeholder="Explain when and where you would like to meet on campus..."
                            >{{ old('message') }}</textarea>

                            <button type="submit" class="btn" style="width: 100%; margin-top: 18px; padding: 13px;">
                                Send Borrow Request
                            </button>
                        </form>
                    @endif
                @endguest

                @auth
                    @if (auth()->id() !== $item->user_id)
                        <div style="margin-top: 20px; pt: 16px; border-top: 1px solid #f0f4f2; text-align: center;">
                            <a href="{{ route('reports.create', ['type' => 'item', 'id' => $item->id]) }}" style="color: #c62828; font-size: 12px; text-decoration: none;">
                                🚩 Report inappropriate listing
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

