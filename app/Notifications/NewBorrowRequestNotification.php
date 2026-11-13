<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBorrowRequestNotification extends Notification
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
        $borrower = $this->borrowRequest->borrower;

        return [
            'type' => 'new_request',
            'title' => 'New Borrow Request',
            'message' => ($borrower ? $borrower->name : 'A student') . " requested to borrow your item '{$item->item_name}' from " . $this->borrowRequest->start_date->format('M d') . " to " . $this->borrowRequest->due_date->format('M d') . ".",
            'link' => route('dashboard', ['tab' => 'incoming']),
            'item_id' => $item->id,
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

