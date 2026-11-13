<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable()->after('email');
            $table->string('phone')->nullable()->after('student_id');
            $table->string('id_card_image')->nullable()->after('phone');
            $table->boolean('is_verified')->default(false)->after('id_card_image');
            $table->string('role')->default('user')->after('is_verified');
            $table->string('status')->default('active')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'phone', 'id_card_image', 'is_verified', 'role', 'status']);
        });
    }
};
