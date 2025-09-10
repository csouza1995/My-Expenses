<?php

use yii\helpers\Url;

/**
 * Tests for Expense Management functionality
 */
class ExpenseCest
{
    /**
     * Setup before each test - login as test user
     */
    public function _before(AcceptanceTester $I)
    {
        // Login as test user before each test
        $I->amOnPage('/auth/login');
        $I->fillField('input[name*="email"]', 'tester@example.com');
        $I->fillField('input[name*="password"]', 'ABCdef123!@#');
        $I->click('button[type="submit"]');

        // Verify we're logged in
        $I->dontSeeInCurrentUrl('/auth/login');
    }

    /**
     * Test expense list page loads correctly
     */
    public function ensureThatExpenseListPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/expense');
        $I->seeResponseCodeIs(200);

        // Should see expense management page
        $I->seeElement('body');
    }
}
