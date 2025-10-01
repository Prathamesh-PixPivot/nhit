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
        Schema::create('reimbursement_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('select_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->string('note_no')->nullable();
            $table->date('date_of_travel')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('mode_of_travel')->nullable();
            $table->string('travel_mode_eligibility')->nullable();
            $table->string('approver_designation')->nullable();
            $table->date('approval_date')->nullable();
            $table->text('purpose_of_travel')->nullable();
            $table->string('adjusted')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('IFSC_code')->nullable();
            $table
                ->enum('status', ['D', 'P', 'A', 'R', 'S'])
                ->nullable()
                ->default('D');
            $table->text('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('reimbursement_expense_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_note_id')->constrained()->onDelete('cascade');
            $table->string('expense_type')->nullable();
            $table->date('bill_date')->nullable();
            $table->string('bill_number')->nullable();
            $table->decimal('bill_amount', 10, 2)->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('supporting_available')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('reimbursement_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reimbursement_note_id')->nullable();
            $table->foreign('reimbursement_note_id')->references('id')->on('reimbursement_notes');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['P', 'A', 'R'])->default('P');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_details');
        Schema::dropIfExists('travel_expenses');
    }
};
