<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blog_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('tag_id');
            $table->primary(['blog_id', 'tag_id']);
            $table->foreign('blog_id')->references('id')->on('blogs')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('blog_tags')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('blog_tag');
    }
};
