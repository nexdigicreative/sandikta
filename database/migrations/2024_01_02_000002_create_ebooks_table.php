<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('year')->nullable();
            $table->string('isbn', 20)->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('cover_image')->nullable();
            $table->string('file_path'); // stored in private storage
            $table->string('file_hash', 64)->nullable(); // SHA-256 hash for integrity
            $table->bigInteger('file_size')->default(0);
            $table->integer('total_pages')->default(0);
            $table->string('kelas_tujuan', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebooks');
    }
};
