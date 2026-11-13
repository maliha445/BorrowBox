<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowRequestStatusNotification extends Notification
{
    use Queueable;

    public $borrowRequest;
    public $status;

    public function __construct(BorrowRequest $borrowRequest, string $status)
    {
        $this->borrowRequest = $borrowRequest;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $item = $this->borrowRequest->item;
        $owner = $this->borrowRequest->owner;

        $ownerName = $owner ? $owner->name : 'The item owner';
        $title = $this->status === 'approved' ? 'Borrow Request Approved! 🎉' : 'Borrow Request Rejected';
        $message = "Your request to borrow '{$item->item_name}' was {$this->status} by {$ownerName}.";

        if ($this->status === 'approved') {
            $message .= " Due date: " . $this->borrowRequest->due_date->format('M d, Y') . ".";
        }

        return [
            'type' => 'request_status_' . $this->status,
            'title' => $title,
            'message' => $message,
            'link' => route('dashboard', ['tab' => $this->status === 'approved' ? 'borrowed' : 'outgoing']),
            'item_id' => $item->id,
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

