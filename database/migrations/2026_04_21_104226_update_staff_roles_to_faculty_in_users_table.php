<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::query()
            ->where('role', 'staff')
            ->update(['role' => 'faculty']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::query()
            ->where('role', 'faculty')
            ->update(['role' => 'staff']);
    }
};
