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
            $table->string('source_id', length: 100);
            $table->string('source_name', length: 100);
            $table->string('author', length: 100);
            $table->string('category', length: 100);
            $table->string('title', length: 512);
            $table->text('description');
            $table->string('url', length: 512);
            $table->string('url_to_image', length: 512);
            $table->text('content');
            $table->date('published_at');

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
