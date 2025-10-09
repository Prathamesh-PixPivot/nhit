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
        Schema::create('vendor_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('account_name')->nullable();
            $table->string('account_number');
            $table->string('account_type')->nullable(); // Savings, Current, etc.
            $table->string('name_of_bank');
            $table->string('branch_name')->nullable();
            $table->string('ifsc_code');
            $table->string('swift_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Ensure only one primary account per vendor
            $table->unique(['vendor_id', 'is_primary'], 'unique_primary_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_accounts');
    }
};
