@extends('layouts.app')

@section('title', 'Leave a Review - BorrowBox')

@section('content')
<div class="container container-narrow">
    <a href="{{ route('dashboard', ['tab' => 'returned']) }}" class="btn btn-sm btn-secondary" style="margin-bottom: 20px;">
        &larr; Back to Dashboard
    </a>

    <h1 class="page-title" style="text-align: center; margin-bottom: 8px;">Rate & Review</h1>
    <p class="page-subtitle" style="text-align: center; margin-bottom: 24px;">
        Help build campus trust by reviewing your experience with {{ $reviewee ? $reviewee->name : 'your peer' }}
    </p>

    <div style="background: #f8faf9; border: 1px solid #e5ebe7; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
        <div style="font-size: 14px; color: #253858; font-weight: bold; margin-bottom: 4px;">
            Item: {{ $borrowRequest->item->item_name }}
        </div>
        <div style="font-size: 13px; color: #667085;">
            Role: Reviewing as <strong>{{ $isBorrower ? 'Borrower' : 'Item Owner' }}</strong> &bull;
            Reviewee: <strong>{{ $reviewee ? $reviewee->name : 'Student' }}</strong>
            ({{ $isBorrower ? 'Owner' : 'Borrower' }})
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reviews.store') }}" method="POST">
        @csrf
        <input type="hidden" name="borrow_request_id" value="{{ $borrowRequest->id }}">

        <label for="rating">Rating</label>
        <select id="rating" name="rating" required style="font-size: 15px;">
            <option value="5" {{ old('rating', '5') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Stars - Excellent Experience</option>
            <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Stars - Very Good</option>
            <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Stars - Average / Decent</option>
            <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 Stars - Below Average</option>
            <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>⭐ 1 Star - Poor / Unpunctual / Damaged</option>
        </select>

        <label for="comment">Your Feedback & Comment</label>
        <textarea 
            id="comment" 
            name="comment" 
            rows="5" 
            placeholder="Share details about punctuality, item condition, communication, and overall experience..." 
            required
        >{{ old('comment') }}</textarea>

        <button type="submit" class="btn" style="width: 100%; margin-top: 22px; padding: 14px;">
            Submit Rating & Review
        </button>
    </form>
</div>
@endsection

