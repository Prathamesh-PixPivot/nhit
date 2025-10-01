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
        Schema::create('green_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->date('order_date')->nullable();
            $table->string('order_no')->nullable();
            $table->decimal('base_value', 15, 2)->nullable();
            $table->decimal('gst', 15, 2)->nullable();
            $table->decimal('other_charges', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('supplier_id')->constrained()->onDelete('cascade');
            $table->string('msme_classification')->nullable();
            $table
                ->enum('protest_note_raised', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table->string('brief_of_goods_services')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_base_value', 15, 2)->nullable();
            $table->decimal('invoice_gst', 15, 2)->nullable();
            $table->decimal('invoice_value', 15, 2)->nullable();
            $table->string('delayed_damages')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('appointed_start_date')->nullable();
            $table->date('supply_period_start')->nullable();
            $table->date('supply_period_end')->nullable();
            $table->string('whether_contract')->nullable();
            $table
                ->enum('extension_contract_period', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table->string('approval_for')->nullable();
            $table->string('budget_expenditure')->nullable();
            $table->string('actual_expenditure')->nullable();
            $table->string('expenditure_over_budget')->nullable();
            $table->string('nature_of_expenses')->nullable();
            $table->string('documents_workdone_supply')->nullable();
            $table->string('documents_discrepancy')->nullable();
            $table->string('amount_submission_non')->nullable();
            $table->string('remarks')->nullable();
            $table->string('auditor_remarks')->nullable();
            $table->string('specify_deviation')->nullable();

            $table
                ->enum('required_submitted', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table
                ->enum('expense_amount_within_contract', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table
                ->enum('milestone_status', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table->text('milestone_remarks')->nullable();
            $table
                ->enum('deviations', ['Y', 'N'])
                ->nullable()
                ->default(null);
            $table
                ->enum('status', ['D', 'PMPL', 'S', 'P', 'A', 'R'])
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
        Schema::dropIfExists('green_notes');
    }
};
