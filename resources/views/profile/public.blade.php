@extends('layouts.app')

@section('title', $user->name . ' - Profile | BorrowBox')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <h1 class="page-title">{{ $user->name }}</h1>
                @if ($user->isVerified())
                    <span class="badge badge-verified">✓ Verified Student</span>
                @else
                    <span class="badge badge-pending">Pending Verification</span>
                @endif
            </div>
            <p class="page-subtitle">Member since {{ $user->created_at->format('M Y') }} · Student ID: {{ substr($user->student_id, 0, 4) . '***' }}</p>
        </div>

        @auth
            @if (auth()->id() !== $user->id)
                <a href="{{ route('reports.create', ['type' => 'user', 'id' => $user->id]) }}" class="btn btn-sm btn-secondary" style="color: #c62828;">
                    🚩 Report User
                </a>
            @endif
        @endauth
    </div>

    <div class="grid-2">
        <div>
            <h2 style="font-size: 18px; margin-bottom: 16px; color: #253858;">Items Shared by {{ $user->name }}</h2>

            @forelse ($items as $item)
                <div style="background: white; border: 1px solid #e5ebe7; border-radius: 10px; padding: 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <a href="{{ route('items.show', $item) }}" style="font-weight: bold; color: #27845a; text-decoration: none; font-size: 16px;">
                            {{ $item->item_name }}
                        </a>
                        <div style="font-size: 13px; color: #667085; margin-top: 4px;">
                            {{ $item->category }} · Condition: {{ $item->condition ?? 'Good' }}
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline">View</a>
                    </div>
                </div>
            @empty
                <p style="color: #667085; font-size: 14px;">No active listings currently available from this student.</p>
            @endforelse
        </div>

        <div>
            <div style="background: #fdfefe; border: 1px solid #e5ebe7; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h2 style="font-size: 18px; margin-bottom: 12px; color: #253858;">Student Rating & Feedback</h2>
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 15px;">
                    <div style="font-size: 40px; font-weight: bold; color: #27845a;">
                        {{ number_format($user->averageRating(), 1) }}
                    </div>
                    <div>
                        <div style="color: #e59819; font-size: 20px;">
                            @php $rating = round($user->averageRating()); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <div style="font-size: 13px; color: #667085;">
                            {{ $user->reviewsReceived->count() }} total reviews
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="font-size: 16px; margin-bottom: 12px; color: #253858;">Reviews from Borrowers & Owners</h3>
            @forelse ($reviews as $review)
                <div style="background: white; border: 1px solid #e5ebe7; border-radius: 8px; padding: 14px; margin-bottom: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-weight: bold; font-size: 14px;">{{ $review->reviewer?->name ?? 'Anonymous' }}</span>
                        <span style="color: #e59819; font-size: 14px;">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </span>
                    </div>
                    <p style="font-size: 14px; color: #4a5568; line-height: 1.5;">{{ $review->comment }}</p>
                    <span style="font-size: 12px; color: #999;">{{ $review->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p style="color: #667085; font-size: 14px;">No reviews yet for this user.</p>
            @endforelse

            <div style="margin-top: 15px;">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

