<?php
// test database configuration using SQLite
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../tests/_output/test.db',
    'charset' => 'utf8',
];
