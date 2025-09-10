<?php

namespace tests\unit\models;

use app\models\Forms\Web\LoginForm;
use app\models\Entities\User;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser()
    {
        $this->model = new LoginForm([
            'email' => 'not_existing_user@example.com',
            'password' => 'not_existing_password',
        ]);

        verify($this->model->login())->false();
        verify(\Yii::$app->user->isGuest)->true();
        verify($this->model->errors)->arrayHasKey('password');
    }

    public function testLoginWrongPassword()
    {
        $this->model = new LoginForm([
            'email' => 'tester@example.com',
            'password' => 'wrong_password',
        ]);

        verify($this->model->login())->false();
        verify(\Yii::$app->user->isGuest)->true();
        verify($this->model->errors)->arrayHasKey('password');
    }

    public function testLoginCorrect()
    {
        $this->model = new LoginForm([
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#',
        ]);

        verify($this->model->login())->true();
        verify(\Yii::$app->user->isGuest)->false();
        verify($this->model->errors)->arrayHasNotKey('password');
    }

    public function testValidationRules()
    {
        // Test required email
        $this->model = new LoginForm(['password' => 'test']);
        verify($this->model->validate())->false();
        verify($this->model->errors)->arrayHasKey('email');

        // Test required password
        $this->model = new LoginForm(['email' => 'test@example.com']);
        verify($this->model->validate())->false();
        verify($this->model->errors)->arrayHasKey('password');

        // Test remember me boolean
        $this->model = new LoginForm([
            'email' => 'test@example.com',
            'password' => 'test',
            'rememberMe' => 'invalid'
        ]);
        verify($this->model->validate())->false();
        verify($this->model->errors)->arrayHasKey('rememberMe');
    }

    public function testRememberMeDefault()
    {
        $this->model = new LoginForm();
        verify($this->model->rememberMe)->true();
    }
}
