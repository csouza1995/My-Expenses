<?php

namespace tests\_support\_helpers;

use Yii;

/**
 * Test Database Helper
 * Ensures test database is properly set up before running tests
 */
class TestDatabaseHelper
{
    private static $initialized = false;

    /**
     * Initialize test database if not already done
     * This method is called automatically before tests run
     */
    public static function setup($verbose = false)
    {
        if (self::$initialized) {
            return;
        }

        $dbPath = __DIR__ . '/../../_output/test.db';

        // Check if database exists and has proper structure
        if (!file_exists($dbPath) || !self::isDatabaseValid($dbPath)) {
            if ($verbose) {
                echo "\n=== Setting up test database ===\n";
            }
            self::createTestDatabase($verbose);
            if ($verbose) {
                echo "=== Test database ready ===\n\n";
            }
        }

        self::$initialized = true;
    }

    /**
     * Check if database exists and has proper structure
     */
    private static function isDatabaseValid($dbPath)
    {
        if (!file_exists($dbPath)) {
            return false;
        }

        try {
            $pdo = new \PDO("sqlite:" . $dbPath);

            // Check if required tables exist
            $tables = ['users', 'expenses', 'category'];
            foreach ($tables as $table) {
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
                if (!$result || $result->rowCount() === 0) {
                    return false;
                }
            }

            // Check if test user exists
            $result = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email = 'tester@example.com'");
            $user = $result->fetch(\PDO::FETCH_ASSOC);

            return $user && $user['count'] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create test database by running setup script
     */
    private static function createTestDatabase($verbose = false)
    {
        // Use direct database creation for silent mode
        self::createDatabaseDirect();
    }

    /**
     * Create database directly without external scripts
     */
    private static function createDatabaseDirect()
    {
        // Load Yii application
        if (!class_exists('Yii')) {
            define('YII_ENV', 'test');
            defined('YII_DEBUG') or define('YII_DEBUG', true);
            require_once __DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php';

            $config = require(__DIR__ . '/../../../config/test.php');
            new \yii\console\Application($config);
        }

        $dbPath = __DIR__ . '/../../_output/test.db';

        // Remove existing database
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }

        // Get database connection
        $db = \Yii::$app->getDb();

        // Create tables
        self::createTables($db);
        self::insertTestData($db);
    }

    /**
     * Create database tables
     */
    private static function createTables($db)
    {
        // Users table
        $db->createCommand("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                auth_key VARCHAR(255),
                access_token VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ")->execute();

        // Expenses table
        $db->createCommand("
            CREATE TABLE expenses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                description TEXT NOT NULL,
                value DECIMAL(10,2) NOT NULL,
                date DATE NOT NULL,
                category_id INTEGER,
                user_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (category_id) REFERENCES category(id)
            )
        ")->execute();

        // Category table
        $db->createCommand("
            CREATE TABLE category (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ")->execute();
    }

    /**
     * Insert test data
     */
    private static function insertTestData($db)
    {
        // Generate proper password hash using Yii2 Security component
        $passwordHash = \Yii::$app->getSecurity()->generatePasswordHash('ABCdef123!@#');
        $authKey = \Yii::$app->getSecurity()->generateRandomString();
        $accessToken = \Yii::$app->getSecurity()->generateRandomString() . '-' . time();

        // Insert test user
        $db->createCommand()->insert('users', [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'tester@example.com',
            'password_hash' => $passwordHash,
            'auth_key' => $authKey,
            'access_token' => $accessToken,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->execute();

        // Insert test categories
        $categories = [
            ['id' => 1, 'name' => 'Food'],
            ['id' => 2, 'name' => 'Transport'],
            ['id' => 3, 'name' => 'Entertainment'],
            ['id' => 4, 'name' => 'Health'],
            ['id' => 5, 'name' => 'Education']
        ];

        foreach ($categories as $category) {
            $db->createCommand()->insert('category', [
                'id' => $category['id'],
                'name' => $category['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();
        }

        // Insert sample expenses for testing
        $expenses = [
            [
                'id' => 1,
                'description' => 'Lunch at restaurant',
                'value' => 25.50,
                'date' => '2025-09-01',
                'category_id' => 1,
                'user_id' => 1
            ],
            [
                'id' => 2,
                'description' => 'Bus ticket',
                'value' => 3.75,
                'date' => '2025-09-02',
                'category_id' => 2,
                'user_id' => 1
            ],
            [
                'id' => 3,
                'description' => 'Movie tickets',
                'value' => 18.00,
                'date' => '2025-09-03',
                'category_id' => 3,
                'user_id' => 1
            ]
        ];

        foreach ($expenses as $expense) {
            $db->createCommand()->insert('expenses', [
                'id' => $expense['id'],
                'description' => $expense['description'],
                'value' => $expense['value'],
                'date' => $expense['date'],
                'category_id' => $expense['category_id'],
                'user_id' => $expense['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();
        }
    }

    /**
     * Reset test database (useful for cleaning between test suites)
     */
    public static function reset()
    {
        self::$initialized = false;
        $dbPath = __DIR__ . '/../../_output/test.db';

        if (file_exists($dbPath)) {
            unlink($dbPath);
        }

        self::setup();
    }

    /**
     * Get test user credentials
     */
    public static function getTestCredentials()
    {
        return [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];
    }

    /**
     * Get test user data for direct database access
     */
    public static function getTestUser()
    {
        $credentials = self::getTestCredentials();

        if (!Yii::$app) {
            return null;
        }

        return Yii::$app->db->createCommand(
            'SELECT * FROM users WHERE email = :email'
        )->bindValue(':email', $credentials['email'])->queryOne();
    }
}
