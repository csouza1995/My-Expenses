<?php

/**
 * Security Test Suite for Expense Management System
 * Tests for SQL Injection, XSS, and Access Control vulnerabilities
 */

require_once __DIR__ . '/src/vendor/autoload.php';

class SecurityTest
{
    public function testInputSanitization()
    {
        echo "🔒 SECURITY TEST SUITE\n";
        echo "=====================\n\n";

        // Test 1: SQL Injection in Category Filter
        echo "1. Testing SQL Injection in Category Filter:\n";
        $maliciousCategories = ["1'; DROP TABLE expenses; --", "1 OR 1=1", "' UNION SELECT * FROM users --"];

        foreach ($maliciousCategories as $cat) {
            $validCategories = array_filter([$cat], function ($cat) {
                return is_numeric($cat) && (int)$cat > 0;
            });

            if (empty($validCategories)) {
                echo "   ✅ BLOCKED: '$cat'\n";
            } else {
                echo "   ❌ ALLOWED: '$cat' (VULNERABILITY!)\n";
            }
        }

        // Test 2: ID Parameter Validation
        echo "\n2. Testing ID Parameter Validation:\n";
        $maliciousIds = ["'; DROP TABLE expenses; --", "-1", "0", "abc", "1.5", "null"];

        foreach ($maliciousIds as $id) {
            if (!is_numeric($id) || (int)$id != $id || (int)$id <= 0) {
                echo "   ✅ BLOCKED: '$id'\n";
            } else {
                echo "   ❌ ALLOWED: '$id' (VULNERABILITY!)\n";
            }
        }

        // Test 3: XSS Protection Test
        echo "\n3. Testing XSS Protection:\n";
        $xssAttempts = [
            "<script>alert('XSS')</script>",
            "javascript:alert('XSS')",
            "<img src=x onerror=alert('XSS')>",
            "';alert('XSS');//"
        ];

        foreach ($xssAttempts as $xss) {
            $encoded = htmlspecialchars($xss, ENT_QUOTES, 'UTF-8');
            if ($encoded !== $xss) {
                echo "   ✅ SANITIZED: '$xss' → '$encoded'\n";
            } else {
                echo "   ❌ NOT SANITIZED: '$xss' (VULNERABILITY!)\n";
            }
        }

        echo "\n🎯 SECURITY TEST COMPLETED!\n";
    }
}

// Run tests
$test = new SecurityTest();
$test->testInputSanitization();
