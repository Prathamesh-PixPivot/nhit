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
        // Only create comments table if it doesn't exist
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('green_note_id')->nullable()->constrained('green_notes')->onDelete('cascade');
                $table->foreignId('payment_note_id')->nullable()->constrained('payment_notes')->onDelete('cascade');
                $table->foreignId('reimbursement_note_id')->nullable()->constrained('reimbursement_notes')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
                $table->text('comment');
                $table->timestamps();
            });
        } else {
            // If table exists, check and add missing columns
            Schema::table('comments', function (Blueprint $table) {
                if (!Schema::hasColumn('comments', 'green_note_id')) {
                    $table->foreignId('green_note_id')->nullable()->constrained('green_notes')->onDelete('cascade');
                }
                if (!Schema::hasColumn('comments', 'payment_note_id')) {
                    $table->foreignId('payment_note_id')->nullable()->constrained('payment_notes')->onDelete('cascade');
                }
                if (!Schema::hasColumn('comments', 'reimbursement_note_id')) {
                    $table->foreignId('reimbursement_note_id')->nullable()->constrained('reimbursement_notes')->onDelete('cascade');
                }
                if (!Schema::hasColumn('comments', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if we created it
        if (Schema::hasTable('comments')) {
            Schema::dropIfExists('comments');
        }
    }
};
