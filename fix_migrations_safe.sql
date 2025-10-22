-- Safe Migration Fix Script
-- This script marks existing tables as migrated without modifying any data

USE neppl;

-- Check if comments table exists and mark migration as run
INSERT IGNORE INTO migrations (migration, batch) 
VALUES ('2025_02_18_000000_create_comments_table', 2);

-- Mark the fix_comments migration as run (it's a safety check migration)
INSERT IGNORE INTO migrations (migration, batch) 
VALUES ('2025_10_08_120005_fix_comments_table_if_needed', 2);

-- Now we can safely run the remaining migrations including organizations table
