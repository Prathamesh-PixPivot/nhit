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
        Schema::create('payment_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('green_note_id')->nullable()->constrained('green_notes')->onDelete('cascade');
            $table->foreignId('reimbursement_note_id')->nullable()->constrained('reimbursement_notes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('note_no')->nullable();
            $table->string('net_payable_round_off')->nullable();
            $table->text('subject')->nullable();
            $table->string('recommendation_of_payment')->nullable();
            $table->json('add_particulars')->nullable();
            $table->json('less_particulars')->nullable();
            $table
                ->enum('status', ['D', 'P', 'A', 'R', 'S'])
                ->nullable()
                ->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_notes');
    }
};