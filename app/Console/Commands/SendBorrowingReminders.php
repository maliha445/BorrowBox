<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Notifications\OverdueItemNotification;
use App\Notifications\UpcomingDueReminderNotification;
use Illuminate\Console\Command;

class SendBorrowingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrowing:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notifications for upcoming return dates (1 day prior) and overdue borrowings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();
        $today = now()->toDateString();

        $upcomingCount = 0;
        $overdueCount = 0;

        // 1. Upcoming due dates (due tomorrow)
        $upcomingRequests = BorrowRequest::where('status', 'approved')
            ->where('due_date', $tomorrow)
            ->with(['item', 'borrower', 'owner'])
            ->get();

        foreach ($upcomingRequests as $req) {
            if ($req->borrower) {
                $req->borrower->notify(new UpcomingDueReminderNotification($req));
                $upcomingCount++;
            }
        }

        // 2. Overdue borrowings
        $overdueRequests = BorrowRequest::whereIn('status', ['approved', 'overdue'])
            ->where('due_date', '<', $today)
            ->with(['item', 'borrower', 'owner'])
            ->get();

        foreach ($overdueRequests as $req) {
            if ($req->status !== 'overdue') {
                $req->update(['status' => 'overdue']);
                if ($req->item) {
                    $req->item->update(['status' => 'overdue']);
                }
            }

            if ($req->borrower) {
                $req->borrower->notify(new OverdueItemNotification($req, 'borrower'));
            }
            if ($req->owner) {
                $req->owner->notify(new OverdueItemNotification($req, 'owner'));
            }
            $overdueCount++;
        }

        $this->info("Reminders sent: {$upcomingCount} upcoming reminder(s), {$overdueCount} overdue notice(s).");

        return Command::SUCCESS;
    }
}
