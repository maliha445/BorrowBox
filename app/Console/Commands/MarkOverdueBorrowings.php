<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Notifications\OverdueItemNotification;
use Illuminate\Console\Command;

class MarkOverdueBorrowings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrowing:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark approved borrow requests past their due date as overdue and update item status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $overdueRequests = BorrowRequest::where('status', 'approved')
            ->where('due_date', '<', $today)
            ->with(['item', 'borrower', 'owner'])
            ->get();

        $count = 0;

        foreach ($overdueRequests as $req) {
            $req->update(['status' => 'overdue']);

            if ($req->item) {
                $req->item->update(['status' => 'overdue']);
            }

            if (class_exists(OverdueItemNotification::class)) {
                if ($req->borrower) {
                    $req->borrower->notify(new OverdueItemNotification($req, 'borrower'));
                }
                if ($req->owner) {
                    $req->owner->notify(new OverdueItemNotification($req, 'owner'));
                }
            }

            $count++;
        }

        $this->info("Successfully updated {$count} borrow request(s) and item(s) to overdue.");

        return Command::SUCCESS;
    }
}
