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
        Schema::create('bank_letter_approval_priorities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_step_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('approver_level');
            $table->timestamps();

            $table->foreign('approval_step_id')->references('id')->on('bank_letter_approval_steps')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_letter_approval_priorities');
    }
};
