@extends('layouts.app')

@section('title', 'Submit Safety Report - BorrowBox')

@section('content')
<div class="container container-narrow">
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary" style="margin-bottom: 20px;">
        &larr; Go Back
    </a>

    <h1 class="page-title" style="text-align: center; margin-bottom: 8px; color: #c62828;">
        🚩 Report {{ $type === 'user' ? 'User' : 'Item Listing' }}
    </h1>
    <p class="page-subtitle" style="text-align: center; margin-bottom: 24px;">
        Help keep our university sharing community safe and respectful
    </p>

    <div style="background: #fff8f8; border: 1px solid #ffd4d4; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
        @if ($type === 'user' && $targetUser)
            <div style="font-size: 14px; color: #253858; font-weight: bold;">
                Reported Student: {{ $targetUser->name }}
            </div>
            <div style="font-size: 13px; color: #667085; margin-top: 4px;">
                Student ID: {{ substr($targetUser->student_id ?? '', 0, 4) . '***' }} &bull; Member since {{ $targetUser->created_at->format('M Y') }}
            </div>
        @elseif ($type === 'item' && $targetItem)
            <div style="font-size: 14px; color: #253858; font-weight: bold;">
                Reported Item: {{ $targetItem->item_name }}
            </div>
            <div style="font-size: 13px; color: #667085; margin-top: 4px;">
                Category: {{ $targetItem->category_display_name }} &bull; Listed by: {{ $targetItem->owner_name }}
            </div>
        @endif
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

    <form action="{{ route('reports.store') }}" method="POST">
        @csrf
        @if ($type === 'user' && $targetUser)
            <input type="hidden" name="reported_user_id" value="{{ $targetUser->id }}">
        @elseif ($type === 'item' && $targetItem)
            <input type="hidden" name="item_id" value="{{ $targetItem->id }}">
        @endif

        <label for="reason">Reason for Report</label>
        <select id="reason" name="reason" required>
            <option value="">Select a reason</option>
            @if ($type === 'user')
                <option value="Unreturned borrowed item">Unreturned borrowed item past due date</option>
                <option value="Returned item damaged or altered">Returned item damaged or altered</option>
                <option value="Harassment or inappropriate communication">Harassment or inappropriate communication</option>
                <option value="Fake or suspicious student credentials">Fake or suspicious student credentials</option>
                <option value="No-show or repeated cancellations">No-show or repeated cancellations</option>
                <option value="Other safety violation">Other campus safety violation</option>
            @else
                <option value="Prohibited or dangerous item">Prohibited or dangerous item</option>
                <option value="Misleading item description or condition">Misleading item description or condition</option>
                <option value="Inappropriate or offensive content">Inappropriate or offensive content / image</option>
                <option value="Suspected stolen property">Suspected stolen property</option>
                <option value="Spam or duplicate listing">Spam or duplicate listing</option>
                <option value="Other concern">Other concern</option>
            @endif
        </select>

        <label for="details">Details & Explanation</label>
        <textarea 
            id="details" 
            name="details" 
            rows="5" 
            placeholder="Please provide specific dates, context, and any messages to help administrators review this matter quickly and fairly..." 
            required
        >{{ old('details') }}</textarea>

        <div style="margin-top: 16px; font-size: 13px; color: #667085; line-height: 1.5;">
            🔒 Reports are strictly confidential and visible only to campus administrators.
        </div>

        <button type="submit" class="btn btn-danger" style="width: 100%; margin-top: 22px; padding: 14px;">
            Submit Report to Administrator
        </button>
    </form>
</div>
@endsection

