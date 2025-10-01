<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dbName = DB::getDatabaseName();
        $hasIndex = function (string $table, string $index) use ($dbName): bool {
            return DB::table('information_schema.statistics')
                ->where('table_schema', $dbName)
                ->where('table_name', $table)
                ->where('index_name', $index)
                ->exists();
        };
        // payments_new
        if (Schema::hasTable('payments_new')) {
            if (!$hasIndex('payments_new', 'payments_new_sl_no_index')) {
                Schema::table('payments_new', fn (Blueprint $t) => $t->index('sl_no'));
            }
            if (Schema::hasColumn('payments_new', 'status') && !$hasIndex('payments_new', 'payments_new_status_index')) {
                Schema::table('payments_new', fn (Blueprint $t) => $t->index('status'));
            }
            if (!$hasIndex('payments_new', 'payments_new_created_at_index')) {
                Schema::table('payments_new', fn (Blueprint $t) => $t->index('created_at'));
            }
            if (Schema::hasColumn('payments_new', 'user_id') && !$hasIndex('payments_new', 'payments_new_user_id_index')) {
                Schema::table('payments_new', fn (Blueprint $t) => $t->index('user_id'));
            }
        }

        // payment_notes
        if (Schema::hasTable('payment_notes')) {
            if (Schema::hasColumn('payment_notes', 'status') && !$hasIndex('payment_notes', 'payment_notes_status_index')) {
                Schema::table('payment_notes', fn (Blueprint $t) => $t->index('status'));
            }
            if (!$hasIndex('payment_notes', 'payment_notes_created_at_index')) {
                Schema::table('payment_notes', fn (Blueprint $t) => $t->index('created_at'));
            }
            if (Schema::hasColumn('payment_notes', 'user_id') && !$hasIndex('payment_notes', 'payment_notes_user_id_index')) {
                Schema::table('payment_notes', fn (Blueprint $t) => $t->index('user_id'));
            }
            if (Schema::hasColumn('payment_notes', 'sl_no') && !$hasIndex('payment_notes', 'payment_notes_sl_no_index')) {
                Schema::table('payment_notes', fn (Blueprint $t) => $t->index('sl_no'));
            }
        }

        // reimbursement_notes
        if (Schema::hasTable('reimbursement_notes')) {
            if (Schema::hasColumn('reimbursement_notes', 'status') && !$hasIndex('reimbursement_notes', 'reimbursement_notes_status_index')) {
                Schema::table('reimbursement_notes', fn (Blueprint $t) => $t->index('status'));
            }
            if (!$hasIndex('reimbursement_notes', 'reimbursement_notes_created_at_index')) {
                Schema::table('reimbursement_notes', fn (Blueprint $t) => $t->index('created_at'));
            }
            if (Schema::hasColumn('reimbursement_notes', 'user_id') && !$hasIndex('reimbursement_notes', 'reimbursement_notes_user_id_index')) {
                Schema::table('reimbursement_notes', fn (Blueprint $t) => $t->index('user_id'));
            }
        }

        // bank_letter_approval_logs
        if (Schema::hasTable('bank_letter_approval_logs')) {
            if (Schema::hasColumn('bank_letter_approval_logs', 'payment_id') && !$hasIndex('bank_letter_approval_logs', 'bank_letter_approval_logs_payment_id_index')) {
                Schema::table('bank_letter_approval_logs', fn (Blueprint $t) => $t->index('payment_id'));
            }
            if (Schema::hasColumn('bank_letter_approval_logs', 'sl_no') && !$hasIndex('bank_letter_approval_logs', 'bank_letter_approval_logs_sl_no_index')) {
                Schema::table('bank_letter_approval_logs', fn (Blueprint $t) => $t->index('sl_no'));
            }
            if (Schema::hasColumn('bank_letter_approval_logs', 'status') && !$hasIndex('bank_letter_approval_logs', 'bank_letter_approval_logs_status_index')) {
                Schema::table('bank_letter_approval_logs', fn (Blueprint $t) => $t->index('status'));
            }
            if (!$hasIndex('bank_letter_approval_logs', 'bank_letter_approval_logs_created_at_index')) {
                Schema::table('bank_letter_approval_logs', fn (Blueprint $t) => $t->index('created_at'));
            }
        }
    }

    public function down(): void
    {
        // Optional: do not drop indexes to avoid downtime when rolling back
    }
};

// Helper: check index existence in a portable-ish way
if (!function_exists('indexExists')) {
    function indexExists(string $table, string $index): bool
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes($table);
        return array_key_exists(strtolower($index), array_change_key_case($indexes, CASE_LOWER));
    }
}


