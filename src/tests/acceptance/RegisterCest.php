<?php

use yii\helpers\Url;

/**
 * Tests for User Registration functionality
 */
class RegisterCest
{
    /**
     * Test register page loads correctly
     */
    public function ensureThatRegisterPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');
        $I->seeResponseCodeIs(200);

        // Should see register page title and content
        $I->see('Registrar-se', 'h1');
        $I->see('Por favor, preencha os seguintes campos para se registrar:');

        // Should see register form elements
        $I->seeElement('#login-form');
        $I->seeElement('input[name*="name"]');
        $I->seeElement('input[name*="email"]');
        $I->seeElement('input[name*="password"]');
        $I->seeElement('button[name="register-button"]');
    }

    /**
     * Test successful user registration
     */
    public function testSuccessfulRegister(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');

        // Generate unique email for this test
        $uniqueEmail = 'test' . time() . '@example.com';

        // Fill register form with valid data
        $I->fillField('input[name*="name"]', 'Test User');
        $I->fillField('input[name*="email"]', $uniqueEmail);
        $I->fillField('input[name*="password"]', 'TestPass123!@#');

        // Submit the form
        $I->click('button[name="register-button"]');

        // Should be redirected to expenses page
        $I->seeInCurrentUrl('/expense');

        // Should see success message
        $I->see('Bem-vindo!');
    }

    /**
     * Test register with empty data shows validation errors
     */
    public function testRegisterWithEmptyData(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');

        // Submit empty form
        $I->click('button[name="register-button"]');

        // Should stay on register page
        $I->seeInCurrentUrl('/auth/register');

        // Should see the form again (validation errors will be shown by JavaScript)
        $I->seeElement('input[name*="name"]');
        $I->seeElement('input[name*="email"]');
        $I->seeElement('input[name*="password"]');
    }

    /**
     * Test register with invalid email
     */
    public function testRegisterWithInvalidEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');

        // Fill form with invalid email
        $I->fillField('input[name*="name"]', 'Test User');
        $I->fillField('input[name*="email"]', 'invalid-email');
        $I->fillField('input[name*="password"]', 'TestPass123!@#');

        // Submit the form
        $I->click('button[name="register-button"]');

        // Should stay on register page due to validation
        $I->seeInCurrentUrl('/auth/register');
    }

    /**
     * Test register with weak password
     */
    public function testRegisterWithWeakPassword(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');

        // Generate unique email
        $uniqueEmail = 'weaktest' . time() . '@example.com';

        // Fill form with weak password
        $I->fillField('input[name*="name"]', 'Test User');
        $I->fillField('input[name*="email"]', $uniqueEmail);
        $I->fillField('input[name*="password"]', '123'); // Too weak

        // Submit the form
        $I->click('button[name="register-button"]');

        // Should stay on register page due to validation
        $I->seeInCurrentUrl('/auth/register');
    }

    /**
     * Test register with existing email
     */
    public function testRegisterWithExistingEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/register');

        // Try to register with existing email
        $I->fillField('input[name*="name"]', 'Test User');
        $I->fillField('input[name*="email"]', 'tester@example.com'); // Existing email
        $I->fillField('input[name*="password"]', 'TestPass123!@#');

        // Submit the form
        $I->click('button[name="register-button"]');

        // Should stay on register page due to validation error
        $I->seeInCurrentUrl('/auth/register');
    }

    /**
     * Test that already logged in users are redirected
     */
    public function testRegisterRedirectWhenLoggedIn(AcceptanceTester $I)
    {
        // First login
        $I->amOnPage('/auth/login');
        $I->fillField('input[name*="email"]', 'tester@example.com');
        $I->fillField('input[name*="password"]', 'ABCdef123!@#');
        $I->click('button[type="submit"]');

        // Verify we're logged in
        $I->dontSeeInCurrentUrl('/auth/login');

        // Now try to access register page
        $I->amOnPage('/auth/register');

        // Should be redirected to home (not register page)
        $I->dontSeeInCurrentUrl('/auth/register');
    }

    /**
     * Test backward compatibility - signup route still works
     */
    public function testBackwardCompatibilitySignupRoute(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/signup');
        $I->seeResponseCodeIs(200);

        // Should see the register page content
        $I->see('Registrar-se', 'h1');
        $I->see('Por favor, preencha os seguintes campos para se registrar:');
    }
}
