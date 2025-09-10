<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../vendor/autoload.php';

// Check and setup test database if needed
$dbPath = __DIR__ . '/_output/test.db';
if (!file_exists($dbPath)) {
    // Run setup script once
    $setupScript = __DIR__ . '/_data/setup_test_db.php';
    if (file_exists($setupScript)) {
        // Capture output to avoid headers already sent issue
        ob_start();
        include $setupScript;
        ob_end_clean();
    }
}
