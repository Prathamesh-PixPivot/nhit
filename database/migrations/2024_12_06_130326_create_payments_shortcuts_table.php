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
        Schema::create('payments_shortcuts', function (Blueprint $table) {
            $table->id();
            $table->string('sl_no')->unique();
            $table->string('shortcut_name');
            $table->json('request_data');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_shortcuts');
    }
};
