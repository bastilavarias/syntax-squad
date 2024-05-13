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
        Schema::create("user_followers", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_following_id");
            $table->unsignedBigInteger("user_follower_id");
            $table->timestamps();

            $table
                ->foreign("user_following_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table
                ->foreign("user_follower_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("user_followers");
    }
};
