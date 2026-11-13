<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UpcomingDueReminderNotification extends Notification
{
    use Queueable;

    public $borrowRequest;

    public function __construct(BorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $item = $this->borrowRequest->item;
        $owner = $this->borrowRequest->owner;

        return [
            'type' => 'upcoming_reminder',
            'title' => 'Reminder: Return Date Tomorrow',
            'message' => "Your borrowed item '{$item->item_name}' is due tomorrow (" . $this->borrowRequest->due_date->format('M d, Y') . "). Please coordinate return with " . ($owner ? $owner->name : 'the owner') . ".",
            'link' => route('dashboard', ['tab' => 'borrowed']),
            'item_id' => $item->id,
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

