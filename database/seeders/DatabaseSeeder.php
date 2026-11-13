<?php

namespace Database\Seeders;

use App\Models\BorrowRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $this->call(CategorySeeder::class);

        $catBooks = Category::where('name', 'Books')->first();
        $catNotes = Category::where('name', 'Academic Notes and Lecture Sheets')->first();
        $catCalcs = Category::where('name', 'Calculators')->first();
        $catElect = Category::where('name', 'Electronics')->first();
        $catLab = Category::where('name', 'Lab Equipment')->first();
        $catSports = Category::where('name', 'Sports Equipment')->first();
        $catStat = Category::where('name', 'Stationery')->first();
        $catOther = Category::where('name', 'Other Useful Campus Items')->first();

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@borrowbox.test'],
            [
                'name' => 'Campus Admin',
                'student_id' => 'ADMIN-001',
                'phone' => '+880 1700-000000',
                'password' => Hash::make('password'),
                'is_verified' => true,
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 3. Sample Verified Students
        $sadia = User::firstOrCreate(
            ['email' => 'sadia@borrowbox.test'],
            [
                'name' => 'Sadia Rahman',
                'student_id' => '2024-10101',
                'phone' => '+880 1711-111111',
                'password' => Hash::make('password'),
                'is_verified' => true,
                'role' => 'user',
                'status' => 'active',
            ]
        );

        $afrin = User::firstOrCreate(
            ['email' => 'afrin@borrowbox.test'],
            [
                'name' => 'Afrin Sultana',
                'student_id' => '2024-10102',
                'phone' => '+880 1722-222222',
                'password' => Hash::make('password'),
                'is_verified' => true,
                'role' => 'user',
                'status' => 'active',
            ]
        );

        $maliha = User::firstOrCreate(
            ['email' => 'maliha@borrowbox.test'],
            [
                'name' => 'Maliha Akter',
                'student_id' => '2024-10103',
                'phone' => '+880 1733-333333',
                'password' => Hash::make('password'),
                'is_verified' => true,
                'role' => 'user',
                'status' => 'active',
            ]
        );

        // 4. Sample Unverified Student (Pending verification)
        $rahul = User::firstOrCreate(
            ['email' => 'rahul@borrowbox.test'],
            [
                'name' => 'Rahul Sen',
                'student_id' => '2024-10104',
                'phone' => '+880 1744-444444',
                'password' => Hash::make('password'),
                'is_verified' => false,
                'role' => 'user',
                'status' => 'active',
            ]
        );

        // 5. Sample Suspended User
        $suspendedUser = User::firstOrCreate(
            ['email' => 'suspended@borrowbox.test'],
            [
                'name' => 'Disruptive User',
                'student_id' => '2024-99999',
                'phone' => '+880 1799-999999',
                'password' => Hash::make('password'),
                'is_verified' => false,
                'role' => 'user',
                'status' => 'suspended',
            ]
        );

        // 6. Sample Items
        $item1 = Item::firstOrCreate(
            ['item_name' => 'Casio FX-991EX Scientific Calculator'],
            [
                'description' => 'Original Casio ClassWiz FX-991EX scientific calculator with solar and battery backup. Ideal for calculus, statistics, and engineering matrix calculations.',
                'category' => $catCalcs->name,
                'category_id' => $catCalcs->id,
                'condition' => 'Like New',
                'user_id' => $sadia->id,
                'owner_name' => $sadia->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item2 = Item::firstOrCreate(
            ['item_name' => "Thomas' Calculus (14th Edition)"],
            [
                'description' => 'Hardcover textbook for Multivariable Calculus courses. Clean pages, no highlighting or torn sheets. Perfect for midterms and final prep.',
                'category' => $catBooks->name,
                'category_id' => $catBooks->id,
                'condition' => 'Good',
                'user_id' => $afrin->id,
                'owner_name' => $afrin->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item3 = Item::firstOrCreate(
            ['item_name' => 'Digital Multimeter UNI-T UT33D+'],
            [
                'description' => 'Compact palm-size digital multimeter with test probes and NCV detector. Great for Electrical and Electronics lab work and circuit testing.',
                'category' => $catElect->name,
                'category_id' => $catElect->id,
                'condition' => 'Like New',
                'user_id' => $maliha->id,
                'owner_name' => $maliha->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item4 = Item::firstOrCreate(
            ['item_name' => 'Organic Chemistry Lab Coat (Size M)'],
            [
                'description' => '100% Cotton white laboratory coat with deep front pockets and safety buttons. Washed and sanitized, ready for chem and biology lab sessions.',
                'category' => $catLab->name,
                'category_id' => $catLab->id,
                'condition' => 'Good',
                'user_id' => $sadia->id,
                'owner_name' => $sadia->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item5 = Item::firstOrCreate(
            ['item_name' => 'Yonex Carbonex Badminton Racket'],
            [
                'description' => 'Durable graphite badminton racket with BG65 stringing at 24lbs tension. Includes full head cover. Great for campus gym matches.',
                'category' => $catSports->name,
                'category_id' => $catSports->id,
                'condition' => 'Good',
                'user_id' => $afrin->id,
                'owner_name' => $afrin->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item6 = Item::firstOrCreate(
            ['item_name' => 'Data Structures & Algorithms Lecture Notes'],
            [
                'description' => 'Comprehensive handwritten lecture sheets and diagram summaries covering Trees, Graphs, Dynamic Programming, and Big-O analysis.',
                'category' => $catNotes->name,
                'category_id' => $catNotes->id,
                'condition' => 'Like New',
                'user_id' => $maliha->id,
                'owner_name' => $maliha->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item7 = Item::firstOrCreate(
            ['item_name' => 'Staedtler Technical Drafting Compass Set'],
            [
                'description' => 'Precision quick-setting compass with extension bar, lead box, and technical pen adapter. Essential for Engineering Drawing & AutoCAD classes.',
                'category' => $catStat->name,
                'category_id' => $catStat->id,
                'condition' => 'New',
                'user_id' => $sadia->id,
                'owner_name' => $sadia->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        $item8 = Item::firstOrCreate(
            ['item_name' => 'Foldable Aluminum Laptop Riser'],
            [
                'description' => 'Sturdy portable laptop riser with 6 adjustable height levels and heat dissipation vents. Fits 11" to 17" laptops and tablets.',
                'category' => $catOther->name,
                'category_id' => $catOther->id,
                'condition' => 'Like New',
                'user_id' => $afrin->id,
                'owner_name' => $afrin->name,
                'is_available' => true,
                'status' => 'available',
                'reward_coins' => 100,
            ]
        );

        // 7. Completed Borrow Request with Reviews
        $completedBorrow = BorrowRequest::create([
            'item_id' => $item1->id,
            'borrower_id' => $afrin->id,
            'owner_id' => $sadia->id,
            'start_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'message' => 'Need this calculator for midterms on Tuesday.',
            'status' => 'returned',
            'approved_at' => now()->subDays(10),
            'returned_at' => now()->subDays(3),
        ]);

        Review::create([
            'borrow_request_id' => $completedBorrow->id,
            'reviewer_id' => $afrin->id,
            'reviewee_id' => $sadia->id,
            'rating' => 5,
            'comment' => 'Sadia was very kind and prompt! The calculator was in perfect condition with full battery. Highly recommend borrowing from her.',
        ]);

        Review::create([
            'borrow_request_id' => $completedBorrow->id,
            'reviewer_id' => $sadia->id,
            'reviewee_id' => $afrin->id,
            'rating' => 5,
            'comment' => 'Afrin returned the calculator right on time and in spotless condition. Very reliable borrower!',
        ]);

        // 8. Active Borrow
        $activeBorrow = BorrowRequest::create([
            'item_id' => $item3->id,
            'borrower_id' => $sadia->id,
            'owner_id' => $maliha->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'message' => 'Doing digital electronics lab project this week.',
            'status' => 'approved',
            'approved_at' => now()->subDays(2),
        ]);
        $item3->update(['status' => 'borrowed', 'is_available' => false]);

        // 9. Sample Pending Safety Report
        Report::create([
            'reporter_id' => $sadia->id,
            'reported_user_id' => $suspendedUser->id,
            'reason' => 'Unreturned borrowed item past due date',
            'details' => 'Student stopped replying to campus messages regarding borrowed lab equipment.',
            'status' => 'pending',
        ]);
    }
}
