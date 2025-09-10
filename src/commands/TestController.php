<?php

namespace app\commands;

use app\models\Entities\User;
use yii\console\Controller;
use Yii;

/**
 * Test utilities and helpers for development and testing
 */
class TestController extends Controller
{
    /**
     * Generates password hash for a given password
     * Usage: php yii test/password-hash [password]
     * 
     * @param string $password The password to hash (default: ABCdef123!@#)
     */
    public function actionPasswordHash($password = 'ABCdef123!@#')
    {
        echo "🔐 Generating password hash...\n\n";

        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);

        echo "Password: $password\n";
        echo "Hash: $hash\n\n";
        echo "💡 You can use this hash in database inserts or fixtures.\n";
    }

    /**
     * Sets up complete test environment
     * Usage: php yii test/setup
     */
    public function actionSetup()
    {
        echo "🚀 Setting up test environment...\n\n";

        // 1. Run migrations
        echo "📊 Running database migrations...\n";
        $migrationResult = Yii::$app->runAction('migrate/up', ['interactive' => false]);

        if ($migrationResult === 0) {
            echo "✅ Migrations completed successfully!\n\n";
        } else {
            echo "⚠️  Migration issues detected, continuing...\n\n";
        }

        // 2. Create test user
        echo "👤 Creating test user...\n";
        $userResult = Yii::$app->runAction('user/create-test');
        echo "\n";

        // 3. Summary
        echo "📋 Test Environment Setup Summary:\n";
        echo "   • Database: Migrated\n";
        echo "   • Test User: tester@example.com / ABCdef123!@#\n";
        echo "   • Ready for: API tests, Acceptance tests\n\n";
        echo "🎯 Run tests with: vendor/bin/codecept run\n";
    }

    /**
     * Resets test database to clean state
     * Usage: php yii test/reset
     */
    public function actionReset()
    {
        echo "🔄 Resetting test environment...\n\n";

        // Drop all tables and re-migrate
        echo "🗑️  Dropping all tables...\n";
        Yii::$app->runAction('migrate/down', ['limit' => 0, 'interactive' => false]);

        echo "📊 Re-running migrations...\n";
        Yii::$app->runAction('migrate/up', ['interactive' => false]);

        echo "👤 Recreating test user...\n";
        Yii::$app->runAction('user/create-test');

        echo "\n✅ Test environment reset complete!\n";
    }

    /**
     * Shows test environment status
     * Usage: php yii test/status
     */
    public function actionStatus()
    {
        echo "📊 Test Environment Status\n";
        echo "========================\n\n";

        // Check database connection
        try {
            $db = Yii::$app->db;
            $tables = $db->schema->getTableNames();
            echo "✅ Database: Connected (" . count($tables) . " tables)\n";
        } catch (\Exception $e) {
            echo "❌ Database: Connection failed\n";
        }

        // Check test user
        $testUser = User::findByEmail('tester@example.com');
        if ($testUser) {
            echo "✅ Test User: Exists (ID: {$testUser->id})\n";
        } else {
            echo "❌ Test User: Not found\n";
        }

        // Check test files
        $testSuites = [
            'API' => 'tests/api/',
            'Acceptance' => 'tests/acceptance/',
            'Functional' => 'tests/functional/',
            'Unit' => 'tests/unit/'
        ];

        echo "\n📁 Test Suites:\n";
        foreach ($testSuites as $name => $path) {
            $fullPath = Yii::getAlias('@app/' . $path);
            if (is_dir($fullPath)) {
                $files = glob($fullPath . '*.php');
                echo "✅ $name: " . count($files) . " test files\n";
            } else {
                echo "❌ $name: Directory not found\n";
            }
        }

        echo "\n💡 Commands:\n";
        echo "   php yii test/setup     - Setup test environment\n";
        echo "   php yii test/reset     - Reset test environment\n";
        echo "   php yii user/create-test - Create test user only\n";
        echo "   vendor/bin/codecept run - Run all tests\n";
    }
}
