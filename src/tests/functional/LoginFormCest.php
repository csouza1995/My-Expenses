<?php

/**
 * Tests for Login Form functionality using Functional Testing
 * Tests model validation and business logic without web interface
 */
class LoginFormCest
{
    /**
     * Setup before each test
     */
    public function _before(\FunctionalTester $I)
    {
        // Clear any existing session
        // Functional tests work with the application directly, so we can just start fresh
    }

    /**
     * Test login page loads correctly
     */
    public function ensureThatLoginPageLoads(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/login');
        $I->seeResponseCodeIs(200);

        // Check page title and basic content
        $I->see('Entrar', 'h1');

        // Check form elements exist with correct IDs
        $I->seeElement('#login-form');
        $I->seeElement('input[name*="email"]');
        $I->seeElement('input[name*="password"]');
        $I->seeElement('input[name*="rememberMe"]');
    }

    /**
     * Test login with empty credentials shows validation errors
     */
    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/login');

        $I->submitForm('#login-form', []);
        $I->expectTo('see validation errors');

        // Should still be on login page
        $I->seeInCurrentUrl('/auth/login');

        // Should see validation messages
        $I->see('O campo Email é obrigatório');
        $I->see('O campo Password é obrigatório');
    }

    /**
     * Test login with wrong credentials shows error
     */
    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/login');

        $I->submitForm('#login-form', [
            'LoginForm[email]' => 'wrong@email.com',
            'LoginForm[password]' => 'wrongpassword',
        ]);
        $I->expectTo('see validation error');

        // Should still be on login page
        $I->seeInCurrentUrl('/auth/login');

        // Should see error message
        $I->see('Email ou senha incorretos');
    }

    /**
     * Test successful login with valid credentials
     */
    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/login');

        $I->submitForm('#login-form', [
            'LoginForm[email]' => 'tester@example.com',
            'LoginForm[password]' => 'ABCdef123!@#',
            'LoginForm[rememberMe]' => 1
        ]);

        $I->expectTo('be redirected after successful login');

        // Should not be on login page anymore
        $I->dontSeeInCurrentUrl('/auth/login');

        // Should be redirected to home or see logged in content
        $I->see('Ver Minhas Despesas');
    }

    /**
     * Test that user can access protected page after login
     */
    public function accessProtectedPageAfterLogin(\FunctionalTester $I)
    {
        // Login first
        $I->amOnRoute('auth/login');
        $I->submitForm('#login-form', [
            'LoginForm[email]' => 'tester@example.com',
            'LoginForm[password]' => 'ABCdef123!@#'
        ]);

        // Now try to access protected expense page
        $I->amOnRoute('expense/index');
        $I->seeResponseCodeIs(200);

        // Should see expense management page
        $I->see('Minhas Despesas');
    }

    /**
     * Test internal login using amLoggedInAs method
     */
    public function internalLoginByUserId(\FunctionalTester $I)
    {
        // Use test user ID (should be 1 for our test user)
        $I->amLoggedInAs(1);

        $I->amOnRoute('site/index');

        // Should see logged in user interface
        $I->dontSee('Entrar');
        $I->see('Ver Minhas Despesas');
    }

    /**
     * Test internal login using User model instance  
     */
    public function internalLoginByUserInstance(\FunctionalTester $I)
    {
        // Find our test user
        $user = \app\models\Entities\User::findByEmail('tester@example.com');
        $I->assertNotNull($user, 'Test user should exist');

        $I->amLoggedInAs($user);

        $I->amOnRoute('expense/index');
        $I->seeResponseCodeIs(200);

        // Should see expense page content
        $I->see('Minhas Despesas');
    }

    /**
     * Test logout functionality
     */
    public function logoutSuccessfully(\FunctionalTester $I)
    {
        // Login first using internal method
        $I->amLoggedInAs(1);

        // Verify we're logged in
        $I->amOnRoute('site/index');
        $I->see('Ver Minhas Despesas');

        // Logout by calling the Yii logout method directly
        \Yii::$app->user->logout();

        // Go to home page to verify logout
        $I->amOnRoute('site/index');

        // Should see guest interface
        $I->see('Entrar');
        $I->dontSee('Ver Minhas Despesas');
    }
}
