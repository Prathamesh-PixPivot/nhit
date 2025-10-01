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
        Schema::create('payment_note_approval_priorities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_step_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('approver_level');
            $table->timestamps();

            $table->foreign('approval_step_id')->references('id')->on('payment_note_approval_steps')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_note_approval_priorities');
    }
};
