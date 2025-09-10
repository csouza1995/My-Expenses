<?php

/**
 * Script for setting up test database
 * This script will:
 * 1. Remove existing test database if it exists
 * 2. Create new test database with proper schema
 * 3. Insert test user with correct password hash
 * 4. Insert sample test data
 */

// Define test environment
if (!defined('YII_ENV')) {
    define('YII_ENV', 'test');
}
if (!defined('YII_DEBUG')) {
    define('YII_DEBUG', true);
}

require_once __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require(__DIR__ . '/../../config/test.php');

// Initialize Yii application for database operations
new yii\console\Application($config);

$dbPath = __DIR__ . '/../_output/test.db';

echo "=== Test Database Setup ===\n";

// Step 1: Remove existing database
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "✓ Removed existing test database\n";
}

// Step 2: Create database connection
$db = Yii::$app->getDb();

// Step 3: Create tables from schema
echo "✓ Creating database schema...\n";

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

echo "✓ Database schema created successfully\n";

// Step 4: Insert test data
echo "✓ Inserting test data...\n";

// Generate proper password hash using Yii2 Security component
$passwordHash = Yii::$app->getSecurity()->generatePasswordHash('ABCdef123!@#');
$authKey = Yii::$app->getSecurity()->generateRandomString();
$accessToken = Yii::$app->getSecurity()->generateRandomString() . '-' . time();

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

echo "✓ Test data inserted successfully\n";

// Step 5: Verify setup
$userCount = $db->createCommand('SELECT COUNT(*) FROM users')->queryScalar();
$categoryCount = $db->createCommand('SELECT COUNT(*) FROM category')->queryScalar();
$expenseCount = $db->createCommand('SELECT COUNT(*) FROM expenses')->queryScalar();

echo "\n=== Database Setup Complete ===\n";
echo "Users: {$userCount}\n";
echo "Categories: {$categoryCount}\n";
echo "Expenses: {$expenseCount}\n";
echo "\nTest credentials:\n";
echo "Email: tester@example.com\n";
echo "Password: ABCdef123!@#\n";
echo "\nDatabase file: {$dbPath}\n";
echo "================================\n";
