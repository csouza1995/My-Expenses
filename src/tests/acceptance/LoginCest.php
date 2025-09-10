<?php

use yii\helpers\Url;

/**
 * Tests for Login functionality
 */
class LoginCest
{
    /**
     * Setup before each test
     */
    public function _before(AcceptanceTester $I)
    {
        // Make sure we're logged out
        $I->amOnPage('/auth/logout');
    }

    /**
     * Test login page loads correctly
     */
    public function ensureThatLoginPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/login');
        $I->seeResponseCodeIs(200);

        // Check form elements exist
        $I->seeElement('input[name*="email"]');
        $I->seeElement('input[name*="password"]');
        $I->seeElement('button[type="submit"]');
    }

    /**
     * Test successful login with valid credentials
     */
    public function ensureThatLoginWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/auth/login');
        $I->seeResponseCodeIs(200);

        $I->amGoingTo('try to login with correct credentials');
        $I->fillField('input[name*="email"]', 'tester@example.com');
        $I->fillField('input[name*="password"]', 'ABCdef123!@#');
        $I->click('button[type="submit"]');

        $I->expectTo('be redirected after successful login');
        // Should not be on login page anymore
        $I->dontSeeInCurrentUrl('/auth/login');
    }

    /**
     * Test logout functionality
     */
    public function ensureThatLogoutWorks(AcceptanceTester $I)
    {
        // First login
        $I->amOnPage('/auth/login');
        $I->fillField('input[name*="email"]', 'tester@example.com');
        $I->fillField('input[name*="password"]', 'ABCdef123!@#');
        $I->click('button[type="submit"]');

        // Now logout
        $I->amOnPage('/auth/logout');

        // Should be redirected to home
        $I->canSeeInCurrentUrl('/');
    }
}
