<?php

/**
 * Tests for Authentication API endpoints
 */
class ApiAuthCest
{
    /**
     * Test login with valid email and password
     */
    public function testLoginSuccess(ApiTester $I)
    {
        $I->wantTo('login with valid email and password');

        $credentials = [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];

        $I->sendPOST('/api/auth/login', $credentials);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Login successful'
        ]);

        // Validate token structure
        $I->seeResponseJsonMatchesJsonPath('$.data.token');
        $I->seeResponseJsonMatchesJsonPath('$.data.user.id');
        $I->seeResponseJsonMatchesJsonPath('$.data.user.username');

        // Check token format (JWT has 3 parts separated by dots)
        $response = json_decode($I->grabResponse(), true);
        $tokenParts = explode('.', $response['data']['token']);
        $I->assertEquals(3, count($tokenParts), 'JWT token should have 3 parts');
    }

    /**
     * Test login with invalid email
     */
    public function testLoginInvalidEmail(ApiTester $I)
    {
        $I->wantTo('fail login with invalid email');

        $I->sendPOST('/api/auth/login', [
            'email' => 'invalid@email.com',
            'password' => 'ABCdef123!@#'
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Validation failed'
        ]);
    }

    /**
     * Test login with invalid password
     */
    public function testLoginInvalidPassword(ApiTester $I)
    {
        $I->wantTo('fail login with invalid password');

        $I->sendPOST('/api/auth/login', [
            'email' => 'tester@example.com',
            'password' => 'wrongpassword'
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Validation failed'
        ]);
    }

    /**
     * Test login with missing fields
     */
    public function testLoginMissingFields(ApiTester $I)
    {
        $I->wantTo('fail login with missing fields');

        // Missing password
        $I->sendPOST('/api/auth/login', [
            'email' => 'tester@example.com'
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false
        ]);
    }

    /**
     * Test token verification with valid token
     */
    public function testVerifyTokenSuccess(ApiTester $I)
    {
        $I->wantTo('verify a valid JWT token');

        // First login to get token
        $credentials = [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];

        $I->sendPOST('/api/auth/login', $credentials);
        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);
        $token = $response['data']['token'];

        // Verify token
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGET('/api/auth/verify');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Token valid'
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.data.user.id');
        $I->seeResponseJsonMatchesJsonPath('$.data.user.username');
    }

    /**
     * Test token verification with invalid token
     */
    public function testVerifyTokenInvalid(ApiTester $I)
    {
        $I->wantTo('fail token verification with invalid token');

        $I->haveHttpHeader('Authorization', 'Bearer invalid-token');
        $I->sendGET('/api/auth/verify');

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Invalid token'
        ]);
    }

    /**
     * Test token refresh with valid token
     */
    public function testRefreshTokenSuccess(ApiTester $I)
    {
        $I->wantTo('refresh a valid JWT token');

        // First login to get token
        $credentials = [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];

        $I->sendPOST('/api/auth/login', $credentials);
        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);
        $token = $response['data']['token'];

        // Refresh token
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendPOST('/api/auth/refresh');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Token refreshed'
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.data.token');

        // Verify new token is valid format
        $newResponse = json_decode($I->grabResponse(), true);
        $newToken = $newResponse['data']['token'];
        $tokenParts = explode('.', $newToken);
        $I->assertEquals(3, count($tokenParts), 'Refreshed token should be valid JWT format');
    }
}
