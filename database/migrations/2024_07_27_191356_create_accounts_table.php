<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('sl_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->timestamp('date')->useCurrent()->nullable();
            // $table->timestamp('date')->useCurrent()->useCurrentOnUpdate()->nullable();
            $table->string('project')->nullable();
            $table->string('invoice_type')->nullable();
            $table->string('account_full_name')->nullable();
            $table->string('from_account_type')->nullable();
            $table->string('full_account_number')->nullable();
            $table->string('to')->nullable();
            $table->string('to_account_type')->nullable();
            $table->string('name_of_beneficiary')->nullable();
            $table->string('account_number')->nullable();
            $table->string('name_of_bank')->nullable();
            $table->string('ifsc_Code_id')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('amount')->nullable();
            $table->string('purpose')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
