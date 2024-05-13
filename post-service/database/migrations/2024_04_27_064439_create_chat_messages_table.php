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
        Schema::create("chat_messages", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("room_id");
            $table->unsignedBigInteger("user_id");
            $table->string("message");
            $table->boolean("read_by_sender")->default(0);
            $table->boolean("read_by_receiver")->default(0);
            $table->timestamps();

            $table
                ->foreign("room_id")
                ->references("id")
                ->on("chat_rooms")
                ->cascadeOnDelete();
            $table
                ->foreign("user_id")
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
        Schema::dropIfExists("chat_messages");
    }
};
