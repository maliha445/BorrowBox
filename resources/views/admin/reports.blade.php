@extends('layouts.app')

@section('title', 'Safety Reports - Admin | BorrowBox')

@section('content')
<div class="container container-wide">
    <div class="page-header">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="color: #27845a; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 6px;">
                &larr; Back to Admin Dashboard
            </a>
            <h1 class="page-title">Safety & Incident Reports</h1>
            <p class="page-subtitle">Review student safety reports, resolve violations, and penalize repeat offenders</p>
        </div>

        <div style="display: flex; gap: 6px;">
            <a href="{{ route('admin.reports', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-danger' : 'btn-secondary' }}">
                Pending Review
            </a>
            <a href="{{ route('admin.reports', ['status' => 'reviewed']) }}" class="btn btn-sm {{ $status === 'reviewed' ? '' : 'btn-secondary' }}">
                Reviewed
            </a>
            <a href="{{ route('admin.reports', ['status' => 'action_taken']) }}" class="btn btn-sm {{ $status === 'action_taken' ? '' : 'btn-secondary' }}">
                Action Taken
            </a>
            <a href="{{ route('admin.reports', ['status' => 'dismissed']) }}" class="btn btn-sm {{ $status === 'dismissed' ? '' : 'btn-secondary' }}">
                Dismissed
            </a>
            <a href="{{ route('admin.reports', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? '' : 'btn-secondary' }}">
                All Reports
            </a>
        </div>
    </div>

    @if ($reports->isEmpty())
        <div style="background: white; border: 1px solid #e5ebe7; border-radius: 12px; padding: 40px; text-align: center; color: #667085;">
            <p>No reports found in this view.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach ($reports as $report)
                <div style="background: white; border: 1px solid {{ $report->status === 'pending' ? '#ffd4d4' : '#e5ebe7' }}; border-radius: 12px; padding: 22px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <span style="font-size: 12px; color: #667085;">Report #{{ $report->id }} &bull; {{ $report->created_at->format('M d, Y H:i') }}</span>
                            <h3 style="font-size: 17px; color: #253858; margin-top: 4px;">
                                Reason: <span style="color: #c62828;">{{ $report->reason }}</span>
                            </h3>
                        </div>

                        <div>
                            @if ($report->status === 'pending')
                                <span class="badge badge-overdue">Pending Admin Review</span>
                            @elseif ($report->status === 'action_taken')
                                <span class="badge badge-verified">Action Taken</span>
                            @elseif ($report->status === 'reviewed')
                                <span class="badge badge-borrowed">Reviewed</span>
                            @else
                                <span class="badge badge-returned">Dismissed</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid-2" style="background: #fcfdfe; border: 1px solid #eef2f0; border-radius: 8px; padding: 14px; margin-bottom: 14px;">
                        <div>
                            <div style="font-size: 13px; color: #667085; margin-bottom: 2px;">Reported Target:</div>
                            @if ($report->reportedUser)
                                <div style="font-size: 15px; font-weight: bold; color: #253858;">
                                    👤 Student: {{ $report->reportedUser->name }}
                                </div>
                                <div style="font-size: 12px; color: #667085; margin-top: 2px;">
                                    Email: {{ $report->reportedUser->email }} &bull; ID: {{ $report->reportedUser->student_id }}
                                </div>

                                {{-- Repeat offender warning --}}
                                @php $repeatCount = $report->reportedUser->reportsReceived()->count(); @endphp
                                @if ($repeatCount > 1)
                                    <div style="margin-top: 6px; display: inline-block; background: #fff0f0; color: #c62828; font-weight: bold; font-size: 12px; padding: 2px 8px; border-radius: 6px; border: 1px solid #ffd4d4;">
                                        ⚠️ Repeat Offender: {{ $repeatCount }} reports received in total!
                                    </div>
                                @endif
                            @elseif ($report->item)
                                <div style="font-size: 15px; font-weight: bold; color: #253858;">
                                    📦 Listing: {{ $report->item->item_name }}
                                </div>
                                <div style="font-size: 12px; color: #667085; margin-top: 2px;">
                                    Owner: {{ $report->item->owner_name }} &bull; Category: {{ $report->item->category_display_name }}
                                </div>
                            @else
                                <span style="color: #999;">Target has been deleted</span>
                            @endif
                        </div>

                        <div>
                            <div style="font-size: 13px; color: #667085; margin-bottom: 2px;">Reporter:</div>
                            <div style="font-size: 14px; font-weight: bold; color: #34483d;">
                                {{ $report->reporter?->name ?? 'Student' }}
                            </div>
                            <div style="font-size: 12px; color: #667085; margin-top: 2px;">
                                Student ID: {{ $report->reporter?->student_id ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 13px; font-weight: bold; color: #253858; margin-bottom: 4px;">Report Details:</div>
                        <p style="font-size: 14px; color: #4a5568; line-height: 1.6; background: #fdfdfe; padding: 12px; border-radius: 8px; border: 1px solid #f0f4f2;">
                            {{ $report->details }}
                        </p>
                    </div>

                    <!-- Admin Actions -->
                    <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; border-top: 1px solid #f0f4f2; pt: 14px; margin-top: 14px; flex-wrap: wrap;">
                        @if ($report->status === 'pending')
                            <form action="{{ route('admin.reports.update', $report) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="reviewed">
                                <button type="submit" class="btn btn-sm btn-secondary">Mark Reviewed</button>
                            </form>

                            <form action="{{ route('admin.reports.update', $report) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="action" value="dismiss">
                                <button type="submit" class="btn btn-sm btn-secondary" style="color: #667085;">Dismiss Report</button>
                            </form>

                            @if ($report->reportedUser && !$report->reportedUser->isAdmin())
                                <form action="{{ route('admin.reports.update', $report) }}" method="POST" style="display:inline;" onsubmit="return confirm('Suspend reported user {{ $report->reportedUser->name }}?');">
                                    @csrf
                                    <input type="hidden" name="action" value="suspend_user">
                                    <button type="submit" class="btn btn-sm btn-danger">Take Action: Suspend User</button>
                                </form>
                            @endif

                            @if ($report->item)
                                <form action="{{ route('admin.reports.update', $report) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete reported listing {{ $report->item->item_name }}?');">
                                    @csrf
                                    <input type="hidden" name="action" value="delete_item">
                                    <button type="submit" class="btn btn-sm btn-danger">Take Action: Delete Listing</button>
                                </form>
                            @endif
                        @else
                            <span style="font-size: 13px; color: #667085;">Status: {{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px;">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection

