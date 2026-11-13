<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountStatusNotification extends Notification
{
    use Queueable;

    public $statusType; // 'verified', 'suspended', 'reactivated'
    public $reason;

    public function __construct(string $statusType, ?string $reason = null)
    {
        $this->statusType = $statusType;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->statusType === 'verified') {
            $title = 'Student Verification Approved! ✓';
            $message = 'Your student ID has been approved by the campus administrator. You can now list items and send borrow requests!';
            $link = route('profile.show');
        } elseif ($this->statusType === 'suspended') {
            $title = 'Account Suspended';
            $message = 'Your BorrowBox account has been suspended by an administrator.' . ($this->reason ? " Reason: {$this->reason}" : '');
            $link = route('profile.show');
        } else {
            $title = 'Account Reactivated';
            $message = 'Your BorrowBox account has been reactivated. You can continue sharing and borrowing items.';
            $link = route('profile.show');
        }

        return [
            'type' => 'account_' . $this->statusType,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ];
    }
}

