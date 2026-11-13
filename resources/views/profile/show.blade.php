@extends('layouts.app')

@section('title', 'My Profile - BorrowBox')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Manage your account information and student verification</p>
        </div>
        <div>
            @if ($user->isVerified())
                <span class="badge badge-verified" style="font-size: 14px; padding: 6px 14px;">✓ Verified Student</span>
            @else
                <span class="badge badge-pending" style="font-size: 14px; padding: 6px 14px;">⏳ Verification Pending</span>
            @endif
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

    <div class="grid-2">
        <div>
            <h2 style="font-size: 18px; margin-bottom: 16px; color: #253858;">Account Details</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>

                <label for="email">Campus Email <span style="font-weight: normal; color: #667085;">(cannot be changed)</span></label>
                <input type="email" id="email" value="{{ $user->email }}" disabled style="background: #f1f3f4; color: #5f6368;">

                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $user->student_id) }}" required>

                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +880 1712-345678">

                <label for="id_card_image">Student ID Card Photo</label>
                <input type="file" id="id_card_image" name="id_card_image" accept="image/*">
                @if ($user->id_card_image)
                    <div style="margin-top: 10px;">
                        <p style="font-size: 12px; color: #667085; margin-bottom: 5px;">Currently uploaded ID card:</p>
                        <a href="{{ asset($user->id_card_image) }}" target="_blank">
                            <img src="{{ asset($user->id_card_image) }}" alt="Student ID" style="max-height: 120px; border-radius: 8px; border: 1px solid #d0d5dd;">
                        </a>
                    </div>
                @endif

                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #eef2f0;">
                    <h3 style="font-size: 15px; color: #253858; margin-bottom: 10px;">Change Password (optional)</h3>
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current">

                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                </div>

                <button type="submit" class="btn" style="margin-top: 22px;">
                    Save Profile Changes
                </button>
            </form>
        </div>

        <div>
            <h2 style="font-size: 18px; margin-bottom: 16px; color: #253858;">Student Verification Status</h2>

            <div style="background: #f9fbf9; border: 1px solid #e2ede5; border-radius: 10px; padding: 18px; margin-bottom: 24px;">
                @if ($user->isVerified())
                    <div style="color: #176b37; font-weight: bold; margin-bottom: 8px; font-size: 16px;">
                        ✓ Account Verified
                    </div>
                    <p style="font-size: 14px; color: #34483d; line-height: 1.6;">
                        Your student ID <strong>({{ $user->student_id }})</strong> has been verified by the administrator. You have full access to list items and request borrows across campus!
                    </p>
                @else
                    <div style="color: #b76e00; font-weight: bold; margin-bottom: 8px; font-size: 16px;">
                        ⏳ Awaiting Admin Approval
                    </div>
                    <p style="font-size: 14px; color: #5a4200; line-height: 1.6;">
                        Your student ID <strong>({{ $user->student_id ?? 'Not provided' }})</strong> is pending verification. Please make sure you have uploaded your student ID card photo so our campus admins can review and approve your account.
                    </p>
                @endif
            </div>

            <div style="background: white; border: 1px solid #e5ebe7; border-radius: 10px; padding: 18px;">
                <h3 style="font-size: 16px; margin-bottom: 12px; color: #253858;">Reputation & Feedback</h3>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="font-size: 36px; font-weight: bold; color: #27845a;">
                        {{ number_format($user->averageRating(), 1) }}
                    </div>
                    <div>
                        <div style="color: #e59819; font-size: 18px;">
                            @php $rating = round($user->averageRating()); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <div style="font-size: 13px; color: #667085;">
                            Based on {{ $user->reviewsReceived->count() }} reviews
                        </div>
                    </div>
                </div>

                <h4 style="font-size: 14px; margin-top: 15px; margin-bottom: 10px; color: #253858;">Recent Reviews</h4>
                @forelse ($reviews as $rev)
                    <div style="border-bottom: 1px solid #f0f0f0; padding: 10px 0; font-size: 13px;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong>{{ $rev->reviewer?->name ?? 'Anonymous' }}</strong>
                            <span style="color: #e59819;">{{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}</span>
                        </div>
                        <p style="color: #667085; margin-top: 4px;">{{ $rev->comment }}</p>
                        <span style="font-size: 11px; color: #999;">{{ $rev->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p style="font-size: 13px; color: #888;">No reviews received yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

