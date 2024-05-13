<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("post_tags", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("post_id");
            $table->string("name");
            $table->timestamps();

            $table
                ->foreign("post_id")
                ->references("id")
                ->on("posts")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("post_tags");
    }
};
