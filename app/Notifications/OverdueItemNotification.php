<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OverdueItemNotification extends Notification
{
    use Queueable;

    public $borrowRequest;
    public $role; // 'borrower' or 'owner'

    public function __construct(BorrowRequest $borrowRequest, string $role = 'borrower')
    {
        $this->borrowRequest = $borrowRequest;
        $this->role = $role;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $item = $this->borrowRequest->item;
        $borrower = $this->borrowRequest->borrower;
        $owner = $this->borrowRequest->owner;

        if ($this->role === 'borrower') {
            $message = "URGENT: Item '{$item->item_name}' was due on " . $this->borrowRequest->due_date->format('M d, Y') . " and is now overdue. Please return it to " . ($owner ? $owner->name : 'the owner') . " immediately.";
        } else {
            $message = "NOTICE: Your item '{$item->item_name}' was due on " . $this->borrowRequest->due_date->format('M d, Y') . " and is now overdue from borrower " . ($borrower ? $borrower->name : 'the borrower') . ".";
        }

        return [
            'type' => 'overdue_item',
            'title' => '⚠️ Item Overdue Notice',
            'message' => $message,
            'link' => route('dashboard', ['tab' => 'overdue']),
            'item_id' => $item->id,
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

