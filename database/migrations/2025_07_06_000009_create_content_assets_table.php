<?php

use App\Enums\StorageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_assets', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('storage')->default(StorageType::LOCAL->value);
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('storage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_assets');
    }
};
