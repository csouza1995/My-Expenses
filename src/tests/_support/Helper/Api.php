<?php

namespace Helper;

use Codeception\Module;

/**
 * API Helper for testing endpoints
 */
class Api extends Module
{
    /**
     * Get the REST module
     */
    private function getRest()
    {
        return $this->getModule('REST');
    }

    /**
     * Generate test user credentials
     */
    public function getTestUserCredentials()
    {
        return [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];
    }

    /**
     * Login and get JWT token
     */
    public function loginAndGetToken()
    {
        $rest = $this->getRest();
        $credentials = $this->getTestUserCredentials();

        $rest->sendPOST('/api/auth/login', $credentials);
        $rest->seeResponseCodeIs(200);
        $rest->seeResponseIsJson();
        $rest->seeResponseContainsJson(['success' => true]);

        $response = json_decode($rest->grabResponse(), true);
        return $response['data']['token'];
    }

    /**
     * Set authorization header with JWT token
     */
    public function setAuthToken($token)
    {
        $rest = $this->getRest();
        $rest->haveHttpHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * Generate test expense data
     */
    public function getTestExpenseData($override = [])
    {
        $default = [
            'description' => 'Test Expense - API Test',
            'category' => 1,
            'value' => 25.50,
            'date' => date('Y-m-d')
        ];

        return array_merge($default, $override);
    }

    /**
     * Create test expense and return ID
     */
    public function createTestExpense($token, $data = [])
    {
        $rest = $this->getRest();
        $this->setAuthToken($token);
        $expenseData = $this->getTestExpenseData($data);

        $rest->sendPOST('/api/expense', $expenseData);
        $rest->seeResponseCodeIs(200);
        $rest->seeResponseIsJson();
        $rest->seeResponseContainsJson(['success' => true]);

        $response = json_decode($rest->grabResponse(), true);
        return $response['data']['expense']['id'];
    }

    /**
     * Validate expense response structure
     */
    public function validateExpenseStructure()
    {
        $rest = $this->getRest();

        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.id');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.description');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.category');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.value');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.date');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.category_name');
        $rest->seeResponseJsonMatchesJsonPath('$.data.expense.date_formatted');
    }

    /**
     * Validate expense list response structure
     */
    public function validateExpenseListStructure()
    {
        $rest = $this->getRest();

        $rest->seeResponseContainsJson([
            'success' => true,
            'message' => 'Success'
        ]);

        $rest->seeResponseJsonMatchesJsonPath('$.data.expenses');
        $rest->seeResponseJsonMatchesJsonPath('$.data.pagination.current_page');
        $rest->seeResponseJsonMatchesJsonPath('$.data.pagination.per_page');
        $rest->seeResponseJsonMatchesJsonPath('$.data.pagination.total_count');
        $rest->seeResponseJsonMatchesJsonPath('$.data.pagination.total_pages');
    }
}
