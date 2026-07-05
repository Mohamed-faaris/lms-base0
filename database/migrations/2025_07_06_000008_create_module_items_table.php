<?php

use App\Enums\ModuleItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default(ModuleItemType::VIDEO->value);
            $table->string('title');
            $table->integer('sort_order')->default(0);
            $table->boolean('required')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_module_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_items');
    }
};
