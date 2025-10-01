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
        Schema::create('payment_note_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_note_id')->nullable();
            $table->foreign('payment_note_id')->references('id')->on('payment_notes');
            $table->foreignId('priority_id')->constrained('payment_note_approval_priorities')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['P', 'A', 'R', 'S'])->default('P');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_note_approval_logs');
    }
};
