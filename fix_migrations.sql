-- Mark the comments migration as already run
INSERT INTO migrations (migration, batch) 
VALUES ('2025_02_18_000000_create_comments_table', 1)
ON DUPLICATE KEY UPDATE migration = migration;
