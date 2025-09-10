<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'messageClass' => 'yii\symfonymailer\Message'
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Entities\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // API Routes
                'POST api/auth/login' => 'api/auth/login',
                'POST api/auth/register' => 'api/auth/register',
                'GET api/auth/verify' => 'api/auth/verify',
                'POST api/auth/refresh' => 'api/auth/refresh',

                'GET api/expense' => 'api/expense/index',
                'POST api/expense' => 'api/expense/create',
                'GET api/expense/<id:\d+>' => 'api/expense/view',
                'PUT api/expense/<id:\d+>' => 'api/expense/update',
                'DELETE api/expense/<id:\d+>' => 'api/expense/delete',

                // Default routes
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
