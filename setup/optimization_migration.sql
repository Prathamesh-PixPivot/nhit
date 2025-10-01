-- Performance Optimization Database Indexes
-- Run this SQL script to add essential indexes for better performance

-- Indexes for payments table
CREATE INDEX IF NOT EXISTS idx_payments_sl_no ON payments_new(sl_no);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments_new(status);
CREATE INDEX IF NOT EXISTS idx_payments_user_id ON payments_new(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_created_at ON payments_new(created_at);
CREATE INDEX IF NOT EXISTS idx_payments_payment_note_id ON payments_new(payment_note_id);
CREATE INDEX IF NOT EXISTS idx_payments_template_type ON payments_new(template_type);
CREATE INDEX IF NOT EXISTS idx_payments_project ON payments_new(project);

-- Composite indexes for common queries
CREATE INDEX IF NOT EXISTS idx_payments_status_user ON payments_new(status, user_id);
CREATE INDEX IF NOT EXISTS idx_payments_sl_no_status ON payments_new(sl_no, status);
CREATE INDEX IF NOT EXISTS idx_payments_created_status ON payments_new(created_at, status);

-- Indexes for green_notes table
CREATE INDEX IF NOT EXISTS idx_green_notes_status ON green_notes(status);
CREATE INDEX IF NOT EXISTS idx_green_notes_user_id ON green_notes(user_id);
CREATE INDEX IF NOT EXISTS idx_green_notes_created_at ON green_notes(created_at);
CREATE INDEX IF NOT EXISTS idx_green_notes_status_user ON green_notes(status, user_id);

-- Indexes for payment_notes table
CREATE INDEX IF NOT EXISTS idx_payment_notes_status ON payment_notes(status);
CREATE INDEX IF NOT EXISTS idx_payment_notes_user_id ON payment_notes(user_id);
CREATE INDEX IF NOT EXISTS idx_payment_notes_green_note_id ON payment_notes(green_note_id);
CREATE INDEX IF NOT EXISTS idx_payment_notes_created_at ON payment_notes(created_at);
CREATE INDEX IF NOT EXISTS idx_payment_notes_status_user ON payment_notes(status, user_id);

-- Indexes for reimbursement_notes table
CREATE INDEX IF NOT EXISTS idx_reimbursement_notes_status ON reimbursement_notes(status);
CREATE INDEX IF NOT EXISTS idx_reimbursement_notes_user_id ON reimbursement_notes(user_id);
CREATE INDEX IF NOT EXISTS idx_reimbursement_notes_approver_id ON reimbursement_notes(approver_id);
CREATE INDEX IF NOT EXISTS idx_reimbursement_notes_created_at ON reimbursement_notes(created_at);

-- Indexes for approval logs
CREATE INDEX IF NOT EXISTS idx_bank_letter_approval_logs_sl_no ON bank_letter_approval_logs(sl_no);
CREATE INDEX IF NOT EXISTS idx_bank_letter_approval_logs_reviewer_id ON bank_letter_approval_logs(reviewer_id);
CREATE INDEX IF NOT EXISTS idx_bank_letter_approval_logs_status ON bank_letter_approval_logs(status);
CREATE INDEX IF NOT EXISTS idx_bank_letter_approval_logs_created_at ON bank_letter_approval_logs(created_at);

-- Indexes for payment approval logs
CREATE INDEX IF NOT EXISTS idx_payment_note_approval_logs_payment_note_id ON payment_note_approval_logs(payment_note_id);
CREATE INDEX IF NOT EXISTS idx_payment_note_approval_logs_reviewer_id ON payment_note_approval_logs(reviewer_id);
CREATE INDEX IF NOT EXISTS idx_payment_note_approval_logs_status ON payment_note_approval_logs(status);

-- Indexes for users table
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
CREATE INDEX IF NOT EXISTS idx_users_department_id ON users(department_id);
CREATE INDEX IF NOT EXISTS idx_users_designation_id ON users(designation_id);

-- Indexes for vendors table
CREATE INDEX IF NOT EXISTS idx_vendors_status ON vendors(status);
CREATE INDEX IF NOT EXISTS idx_vendors_project ON vendors(project);
CREATE INDEX IF NOT EXISTS idx_vendors_vendor_type ON vendors(vendor_type);
CREATE INDEX IF NOT EXISTS idx_vendors_from_account_type ON vendors(from_account_type);
CREATE INDEX IF NOT EXISTS idx_vendors_s_no ON vendors(s_no);

-- Indexes for activity logs
CREATE INDEX IF NOT EXISTS idx_activity_log_subject_type ON activity_log(subject_type);
CREATE INDEX IF NOT EXISTS idx_activity_log_subject_id ON activity_log(subject_id);
CREATE INDEX IF NOT EXISTS idx_activity_log_causer_id ON activity_log(causer_id);
CREATE INDEX IF NOT EXISTS idx_activity_log_created_at ON activity_log(created_at);

-- Indexes for tickets
CREATE INDEX IF NOT EXISTS idx_tickets_status ON tickets(status);
CREATE INDEX IF NOT EXISTS idx_tickets_user_id ON tickets(user_id);
CREATE INDEX IF NOT EXISTS idx_tickets_created_at ON tickets(created_at);

-- Indexes for comments
CREATE INDEX IF NOT EXISTS idx_comments_ticket_id ON ticket_comments(ticket_id);
CREATE INDEX IF NOT EXISTS idx_comments_user_id ON ticket_comments(user_id);
CREATE INDEX IF NOT EXISTS idx_comments_created_at ON ticket_comments(created_at);

-- Optimize table structures
ALTER TABLE payments_new ENGINE=InnoDB;
ALTER TABLE green_notes ENGINE=InnoDB;
ALTER TABLE payment_notes ENGINE=InnoDB;
ALTER TABLE reimbursement_notes ENGINE=InnoDB;
ALTER TABLE users ENGINE=InnoDB;
ALTER TABLE vendors ENGINE=InnoDB;

-- Set proper charset and collation for better performance
ALTER TABLE payments_new CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE green_notes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE payment_notes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE reimbursement_notes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE vendors CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
