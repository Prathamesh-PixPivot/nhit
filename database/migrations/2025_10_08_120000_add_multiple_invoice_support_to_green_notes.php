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
        Schema::table('green_notes', function (Blueprint $table) {
            // Add JSON field for multiple invoices
            $table->json('invoices')->nullable()->after('invoice_other_charges');
            
            // Keep existing fields for backward compatibility but make them nullable
            $table->string('invoice_number')->nullable()->change();
            $table->date('invoice_date')->nullable()->change();
            $table->decimal('invoice_base_value', 15, 2)->nullable()->change();
            $table->decimal('invoice_gst', 15, 2)->nullable()->change();
            $table->decimal('invoice_value', 15, 2)->nullable()->change();
            $table->decimal('invoice_other_charges', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('green_notes', function (Blueprint $table) {
            $table->dropColumn('invoices');
        });
    }
};
