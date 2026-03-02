<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streaks', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('count');
            $table->date('date');
            $table->timestamps();

            $table->primary(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streaks');
    }
};
