@extends('layouts.app')

@section('title', 'Notifications - BorrowBox')

@section('content')
<div class="container container-narrow">
    <div class="page-header">
        <div>
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Stay updated on your borrowing requests, approvals, and reminders</p>
        </div>

        @if ($unreadCount > 0)
            <div>
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">
                        Mark All as Read
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 40px; text-align: center; color: #667085;">
            <div style="font-size: 40px; margin-bottom: 12px;">🔔</div>
            <h3>No notifications yet</h3>
            <p style="margin-top: 6px;">You will receive updates when students request your items or when your requests are approved.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                @endphp
                <div style="background: {{ $isUnread ? '#f4faf6' : 'white' }}; border: 1px solid {{ $isUnread ? '#c2ebd1' : '#e5ebe7' }}; border-radius: 10px; padding: 16px; display: flex; justify-content: space-between; align-items: flex-start; gap: 14px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <strong style="font-size: 15px; color: #253858;">
                                {{ $data['title'] ?? 'Notification' }}
                            </strong>
                            @if ($isUnread)
                                <span class="badge badge-verified" style="font-size: 10px; padding: 1px 6px;">New</span>
                            @endif
                        </div>

                        <p style="font-size: 14px; color: #4a5568; line-height: 1.5; margin-bottom: 8px;">
                            {{ $data['message'] ?? '' }}
                        </p>

                        <div style="font-size: 12px; color: #8898aa;">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                        @if (!empty($data['link']))
                            <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-outline">
                                View
                            </a>
                        @endif

                        @if ($isUnread)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: none; border: none; font-size: 11px; color: #667085; cursor: pointer; text-decoration: underline;">
                                    Mark read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px;">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

