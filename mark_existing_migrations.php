<?php

// Script to safely mark existing database tables as migrated
// This preserves all your data while fixing the migration tracking

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$migrations = [
    '2025_04_21_111608_create_bank_letter_approval_steps_table',
    '2025_04_21_111658_create_bank_letter_approval_priorities_table',
    '2025_04_22_101232_create_bank_letter_approval_logs_table',
    '2025_04_24_072314_create_bank_letter_log_priority_table',
    '2025_06_12_070534_add_session_id_to_users_table',
    '2025_07_10_084055_create_tickets_table',
    '2025_07_11_063213_create_ticket_comments_table',
    '2025_07_11_124059_create_ticket_status_logs_table',
    '2025_09_24_000001_add_active_to_users_table',
    '2025_09_24_000002_create_payments_new_minimal_table',
    '2025_09_24_000003_add_performance_indexes',
    '2025_09_24_060633_create_payments_new_minimal',
];

echo "Marking existing tables as migrated (preserving all data)...\n";

foreach ($migrations as $migration) {
    try {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 2
        ]);
        echo "✓ Marked: $migration\n";
    } catch (\Exception $e) {
        echo "- Already marked: $migration\n";
    }
}

echo "\nDone! All existing tables are now marked as migrated.\n";
echo "You can now run: php artisan migrate --force\n";
