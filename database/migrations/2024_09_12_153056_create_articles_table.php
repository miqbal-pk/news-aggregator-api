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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_id', length: 256)->nullable();
            $table->string('source_name', length: 256)->nullable();
            $table->string('author', length: 256)->nullable();
            $table->string('category', length: 100)->nullable();
            $table->string('title', length: 512)->nullable();
            $table->text('description')->nullable();
            $table->string('url', length: 512)->nullable();
            $table->string('url_to_image', length: 512)->nullable();
            $table->text('content')->nullable();
            $table->date('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
