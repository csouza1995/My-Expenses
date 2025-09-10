#!/usr/bin/env php
<?php
/**
 * Test Database Reset Script
 * 
 * Usage: php reset_test_db.php
 * 
 * This script will:
 * - Remove existing test database
 * - Create fresh test database with schema
 * - Insert test user and sample data
 */

echo "=== Test Database Reset Tool ===\n";

$setupScript = __DIR__ . '/setup_test_db.php';

if (!file_exists($setupScript)) {
    echo "❌ Error: Setup script not found at {$setupScript}\n";
    exit(1);
}

echo "🔄 Resetting test database...\n";

// Run the setup script
require $setupScript;

echo "✅ Test database reset complete!\n";
echo "\nYou can now run tests with:\n";
echo "  vendor/bin/codecept run\n";
echo "  vendor/bin/codecept run api\n";
echo "  vendor/bin/codecept run unit\n";
echo "  vendor/bin/codecept run functional\n";
echo "  vendor/bin/codecept run acceptance\n";
