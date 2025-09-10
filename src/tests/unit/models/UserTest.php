<?php

namespace tests\unit\models;

use app\models\Entities\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        verify($user = User::findIdentity(1))->notEmpty();
        verify($user->email)->equals('tester@example.com');

        verify(User::findIdentity(999))->empty();
    }

    public function testFindUserByEmail()
    {
        verify($user = User::findByEmail('tester@example.com'))->notEmpty();
        verify($user->email)->equals('tester@example.com');

        verify(User::findByEmail('not-existing@example.com'))->empty();
    }

    public function testValidatePassword()
    {
        $user = User::findByEmail('tester@example.com');

        verify($user->validatePassword('ABCdef123!@#'))->true();
        verify($user->validatePassword('wrong_password'))->false();
    }

    public function testCreateUser()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'newuser@example.com';
        $user->setPassword('Test123!@#');

        verify($user->validate())->true();
        verify($user->name)->equals('Test User');
        verify($user->email)->equals('newuser@example.com');
        verify($user->validatePassword('Test123!@#'))->true();
    }

    public function testUserValidation()
    {
        $user = new User();

        // User model has validation rules (name and email are required)
        // so validation should fail with empty user
        verify($user->validate())->false();

        // Test user creation with valid data
        $user->name = 'Valid User';
        $user->email = 'valid@example.com';
        $user->setPassword('Valid123!@#');
        verify($user->validate())->true();

        // Test that properties are set correctly
        verify($user->name)->equals('Valid User');
        verify($user->email)->equals('valid@example.com');
        verify($user->password_hash)->notEmpty();
    }

    public function testPasswordHashing()
    {
        $user = new User();
        $password = 'TestPassword123!@#';

        $user->setPassword($password);

        verify($user->password_hash)->notEmpty();
        verify($user->password_hash)->notEquals($password);
        verify($user->validatePassword($password))->true();
        verify($user->validatePassword('wrong_password'))->false();
    }
}
