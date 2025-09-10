<?php

/**
 * Tests for Register Form functionality using Functional Testing
 */
class RegisterFormCest
{
    /**
     * Setup before each test
     */
    public function _before(\FunctionalTester $I)
    {
        // Clear any existing session
    }

    /**
     * Test register page loads correctly
     */
    public function ensureThatRegisterPageLoads(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');
        $I->seeResponseCodeIs(200);

        // Check page title and basic content
        $I->see('Registrar-se', 'h1');
        $I->see('Por favor, preencha os seguintes campos para se registrar:');

        // Check form elements exist
        $I->seeElement('#login-form');
        $I->seeElement('input[name*="name"]');
        $I->seeElement('input[name*="email"]');
        $I->seeElement('input[name*="password"]');
        $I->seeElement('button[name="register-button"]');
    }

    /**
     * Test register with empty credentials shows validation errors
     */
    public function registerWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        // Submit empty form
        $I->submitForm('#login-form', []);

        $I->comment('I expect to see validation errors');
        $I->seeInCurrentUrl('/auth/register');
        $I->see('O campo Name é obrigatório');
        $I->see('O campo Email é obrigatório');
        $I->see('O campo Password é obrigatório');
    }

    /**
     * Test register with invalid email
     */
    public function registerWithInvalidEmail(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'invalid-email',
            'RegisterForm[password]' => 'ValidPass123!@#',
        ]);

        $I->comment('I expect to see email validation error');
        $I->seeInCurrentUrl('/auth/register');
        $I->see('Por favor, insira um endereço de e-mail válido');
    }

    /**
     * Test register with weak password
     */
    public function registerWithWeakPassword(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'test' . time() . '@example.com',
            'RegisterForm[password]' => '123', // Too weak
        ]);

        $I->comment('I expect to see password validation errors');
        $I->seeInCurrentUrl('/auth/register');
        // The validation message appears in English in the test environment
        $I->see('Password should contain at least 6 characters');
    }

    /**
     * Test register with existing email
     */
    public function registerWithExistingEmail(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'tester@example.com', // Existing email
            'RegisterForm[password]' => 'ValidPass123!@#',
        ]);

        $I->comment('I expect to see email uniqueness validation error');
        $I->seeInCurrentUrl('/auth/register');
        $I->see('Este endereço de e-mail já está em uso');
    }

    /**
     * Test successful register
     */
    public function registerSuccessfully(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        // Generate unique email for this test
        $uniqueEmail = 'functest' . time() . '@example.com';

        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Functional Test User',
            'RegisterForm[email]' => $uniqueEmail,
            'RegisterForm[password]' => 'FuncTestPass123!@#',
        ]);

        $I->comment('I expect to be redirected after successful register');
        $I->dontSeeInCurrentUrl('/auth/register');
        $I->seeInCurrentUrl('/expense');
        $I->see('Bem-vindo!');
    }

    /**
     * Test that already logged in users are redirected from register page
     */
    public function registerRedirectWhenLoggedIn(\FunctionalTester $I)
    {
        // First login as existing user
        $user = \app\models\Entities\User::findByEmail('tester@example.com');
        if ($user) {
            $I->amLoggedInAs($user);
        }

        // Try to access register page
        $I->amOnRoute('auth/register');

        // Should be redirected to home (not register page)
        $I->dontSeeInCurrentUrl('/auth/register');
    }

    /**
     * Test register form password validation rules
     */
    public function testPasswordValidationRules(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/register');

        // Test password without number
        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'testpass1' . time() . '@example.com',
            'RegisterForm[password]' => 'NoNumberPass!@#',
        ]);

        $I->seeInCurrentUrl('/auth/register');
        $I->see('A senha deve conter pelo menos um número');

        // Test password without uppercase
        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'testpass2' . time() . '@example.com',
            'RegisterForm[password]' => 'nouppercase123!@#',
        ]);

        $I->seeInCurrentUrl('/auth/register');
        $I->see('A senha deve conter pelo menos uma letra maiúscula');

        // Test password without special character
        $I->submitForm('#login-form', [
            'RegisterForm[name]' => 'Test User',
            'RegisterForm[email]' => 'testpass3' . time() . '@example.com',
            'RegisterForm[password]' => 'NoSpecialChar123',
        ]);

        $I->seeInCurrentUrl('/auth/register');
        $I->see('A senha deve conter pelo menos um caractere especial');
    }

    /**
     * Test backward compatibility - signup route still works
     */
    public function testBackwardCompatibilitySignupRoute(\FunctionalTester $I)
    {
        $I->amOnRoute('auth/signup');
        $I->seeResponseCodeIs(200);

        // Should see the register page content
        $I->see('Registrar-se', 'h1');
        $I->see('Por favor, preencha os seguintes campos para se registrar:');
    }
}
