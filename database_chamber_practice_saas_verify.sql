-- =============================================================================
-- Verify Chamber / practice SaaS tables (matches database_chamber_practice_saas_patch.sql)
-- Usage:
--   mysql -h HOST -u USER -p programm_prod < database_chamber_practice_saas_verify.sql
-- Or from mysql client: SOURCE database_chamber_practice_saas_verify.sql;
-- =============================================================================

SET NAMES utf8mb4;

SELECT DATABASE() AS current_database;

SELECT
  t.table_name,
  'OK' AS status
FROM information_schema.tables t
WHERE t.table_schema = DATABASE()
  AND t.table_name IN (
    'doctor_portal_profile',
    'doctor_chamber',
    'chamber_serial_queue',
    'chamber_queue_ticker',
    'booking_otp_session',
    'visit_vital',
    'patient_practice_tag',
    'doctor_schedule_exception',
    'prescription_print_template',
    'prescription_favorite',
    'subscription_plan',
    'doctor_subscription',
    'sms_credit_ledger',
    'usage_analytics_event',
    'referral_lab_event',
    'medicine_reminder',
    'bd_payment_intent'
  )
ORDER BY t.table_name;

SELECT
  17 AS expected_tables,
  COUNT(*) AS found_tables
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN (
    'doctor_portal_profile',
    'doctor_chamber',
    'chamber_serial_queue',
    'chamber_queue_ticker',
    'booking_otp_session',
    'visit_vital',
    'patient_practice_tag',
    'doctor_schedule_exception',
    'prescription_print_template',
    'prescription_favorite',
    'subscription_plan',
    'doctor_subscription',
    'sms_credit_ledger',
    'usage_analytics_event',
    'referral_lab_event',
    'medicine_reminder',
    'bd_payment_intent'
  );
