<?php

use yii\helpers\Url;

/**
 * Tests for Security and Access Control
 */
class SecurityCest
{
    /**
     * Test that error pages don't expose sensitive information
     */
    public function ensureThatErrorPagesAreSafe(AcceptanceTester $I)
    {
        // Access non-existent page
        $I->amOnPage('/nonexistent-page');

        // Should see 404 error without exposing system details
        $I->seeResponseCodeIs(404);

        // Should not see sensitive information like file paths
        $I->dontSee('/var/www');
        $I->dontSee('Stack trace');
    }
}
