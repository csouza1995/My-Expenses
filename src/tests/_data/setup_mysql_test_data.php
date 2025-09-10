<?php

/**
 * Script for setting up test data in MySQL database for acceptance tests
 * This ensures the web interface (accessed via nginx) has the same test data
 * as the SQLite database used by other tests
 */

// Define test environment
if (!defined('YII_ENV')) {
    define('YII_ENV', 'dev');
}
if (!defined('YII_DEBUG')) {
    define('YII_DEBUG', true);
}

require_once __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require(__DIR__ . '/../../config/web.php');

// Initialize Yii application
new yii\web\Application($config);

echo "=== MySQL Test Data Setup ===\n";

$db = Yii::$app->getDb();

// Clean existing test data
echo "🔄 Cleaning existing test data...\n";
$db->createCommand("DELETE FROM expenses WHERE user_id IN (SELECT id FROM users WHERE email = 'tester@example.com')")->execute();
$db->createCommand("DELETE FROM users WHERE email = 'tester@example.com'")->execute();

// Generate proper password hash
$passwordHash = Yii::$app->getSecurity()->generatePasswordHash('ABCdef123!@#');
$authKey = Yii::$app->getSecurity()->generateRandomString();
$accessToken = Yii::$app->getSecurity()->generateRandomString() . '-' . time();

// Insert test user
echo "👤 Creating test user...\n";
$db->createCommand()->insert('users', [
    'name' => 'Test User',
    'email' => 'tester@example.com',
    'password_hash' => $passwordHash,
    'auth_key' => $authKey,
    'access_token' => $accessToken,
    'created_at' => time(),
    'updated_at' => time()
])->execute();

$userId = $db->getLastInsertID();

// Categories are handled by ExpenseCategoriesEnum, no table needed!
echo "📂 Categories will use enum values (1=Alimentação, 2=Transporte, 5=Lazer)...\n";

// Insert sample expenses (using enum category values)
echo "💰 Creating sample expenses...\n";
$expenses = [
    [
        'description' => 'Lunch at restaurant',
        'value' => 25.50,
        'date' => '2025-09-01',
        'category' => 1, // ALIMENTACAO
    ],
    [
        'description' => 'Bus ticket',
        'value' => 3.75,
        'date' => '2025-09-02',
        'category' => 2, // TRANSPORTE
    ],
    [
        'description' => 'Movie tickets',
        'value' => 18.00,
        'date' => '2025-09-03',
        'category' => 5, // LAZER
    ]
];

foreach ($expenses as $expense) {
    $db->createCommand()->insert('expenses', [
        'description' => $expense['description'],
        'value' => $expense['value'],
        'date' => $expense['date'],
        'category' => $expense['category'],
        'user_id' => $userId,
        'created_at' => time(),
        'updated_at' => time()
    ])->execute();
}

// Verify setup
$userCount = $db->createCommand('SELECT COUNT(*) FROM users WHERE email = "tester@example.com"')->queryScalar();
$expenseCount = $db->createCommand('SELECT COUNT(*) FROM expenses WHERE user_id = :userId')
    ->bindValue(':userId', $userId)
    ->queryScalar();

echo "\n✅ MySQL Test Data Setup Complete!\n";
echo "Test user created: {$userCount}\n";
echo "Sample expenses: {$expenseCount}\n";
echo "\nTest credentials:\n";
echo "Email: tester@example.com\n";
echo "Password: ABCdef123!@#\n";
echo "================================\n";
