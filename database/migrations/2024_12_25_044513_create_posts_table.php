<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();  // id поста
        $table->string('title');  // заголовок поста
        $table->string('slug')->unique();  // уникальный slug (автогенерация на основе title)
        $table->text('content');  // текст поста
        $table->timestamps();  // created_at и updated_at
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
