<?php

use yii\helpers\Url;

/**
 * Tests for Home page functionality
 */
class HomeCest
{
    /**
     * Test home page loads correctly for guest users
     */
    public function ensureThatHomePageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);

        // Check if we can see basic HTML structure
        $I->seeElement('body');

        // Check navigation for guest users (login should exist)
        $I->seeLink('Entrar');
    }

    /**
     * Test navigation to about page
     */
    public function ensureThatAboutPageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/site/about');
        $I->seeResponseCodeIs(200);

        // Should see about page content
        $I->seeElement('body');
    }

    /**
     * Test navigation links work correctly
     */
    public function ensureThatNavigationWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');

        // Test login link
        $I->click('Entrar');
        $I->seeInCurrentUrl('/auth/login');
        $I->seeResponseCodeIs(200);

        // Test navigation back to home
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
    }
}
