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
        Schema::create('supporting_docs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('green_note_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('file_path');

            $table->foreign('green_note_id')->references('id')->on('green_notes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporting_docs');
    }
};
