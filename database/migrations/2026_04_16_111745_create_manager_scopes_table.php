<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('college');
            $table->string('department')->nullable();
            $table->timestamps();

            $table->unique(['manager_user_id', 'college', 'department'], 'manager_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_scopes');
    }
};
