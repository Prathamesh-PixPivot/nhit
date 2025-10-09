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
            // Modify status enum to include 'H' for Hold
            $table->enum('status', ['D', 'PMPL', 'S', 'P', 'A', 'R', 'H'])
                ->nullable()
                ->default(null)
                ->change();
                
            // Add hold reason field
            $table->text('hold_reason')->nullable()->after('status');
            $table->timestamp('hold_date')->nullable()->after('hold_reason');
            $table->unsignedBigInteger('hold_by')->nullable()->after('hold_date');
            
            // Add foreign key for hold_by
            $table->foreign('hold_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('green_notes', function (Blueprint $table) {
            $table->dropForeign(['hold_by']);
            $table->dropColumn(['hold_reason', 'hold_date', 'hold_by']);
            
            // Revert status enum
            $table->enum('status', ['D', 'PMPL', 'S', 'P', 'A', 'R'])
                ->nullable()
                ->default(null)
                ->change();
        });
    }
};
