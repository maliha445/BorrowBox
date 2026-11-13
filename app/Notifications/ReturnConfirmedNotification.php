<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReturnConfirmedNotification extends Notification
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
            'type' => 'return_confirmed',
            'title' => 'Return Confirmed! Please Review',
            'message' => ($owner ? $owner->name : 'The owner') . " confirmed that you returned '{$item->item_name}'. Click here to leave a review and rating!",
            'link' => route('reviews.create', ['borrow_request_id' => $this->borrowRequest->id]),
            'item_id' => $item->id,
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

