<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ebook_id')->constrained()->onDelete('cascade');
            $table->integer('last_page')->default(1);
            $table->integer('duration_seconds')->default(0); // total reading time
            $table->timestamp('last_read_at')->nullable();
            $table->integer('read_count')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'ebook_id']);
            $table->index(['user_id', 'last_read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_histories');
    }
};
