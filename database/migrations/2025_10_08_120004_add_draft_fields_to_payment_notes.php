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
        Schema::table('payment_notes', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('payment_notes', 'is_draft')) {
                $table->boolean('is_draft')->default(false)->after('status');
            }
            if (!Schema::hasColumn('payment_notes', 'auto_created')) {
                $table->boolean('auto_created')->default(false)->after('is_draft');
            }
            if (!Schema::hasColumn('payment_notes', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('auto_created');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('payment_notes', 'utr_no')) {
                $table->string('utr_no')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('payment_notes', 'utr_date')) {
                $table->date('utr_date')->nullable()->after('utr_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_notes', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['is_draft', 'auto_created', 'created_by', 'utr_no', 'utr_date']);
        });
    }
};
