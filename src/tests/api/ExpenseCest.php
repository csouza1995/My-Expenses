<?php

/**
 * Tests for Expense CRUD endpoints via API
 */
class ApiExpenseCest
{
    private $token;
    private $testExpenseIds = [];

    /**
     * Setup before each test - Login and get token
     */
    public function _before(ApiTester $I)
    {
        // First login to get token
        $credentials = [
            'email' => 'tester@example.com',
            'password' => 'ABCdef123!@#'
        ];

        $I->sendPOST('/api/auth/login', $credentials);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);

        $response = json_decode($I->grabResponse(), true);
        $this->token = $response['data']['token'];

        // Set authorization header for subsequent requests
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
    }

    /**
     * Cleanup after each test
     */
    public function _after(ApiTester $I)
    {
        // Clean up created test expenses
        foreach ($this->testExpenseIds as $expenseId) {
            $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
            $I->sendDELETE("/api/expense/{$expenseId}");
        }
        $this->testExpenseIds = [];
    }

    /**
     * Test creating a new expense
     */
    public function testCreateExpenseSuccess(ApiTester $I)
    {
        $I->wantTo('create a new expense');

        $expenseData = [
            'description' => 'New expense via API test',
            'category' => 1,
            'value' => 45.99,
            'date' => date('Y-m-d')
        ];

        $I->sendPOST('/api/expense', $expenseData);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Expense created successfully'
        ]);

        // Validate expense response structure
        $I->seeResponseJsonMatchesJsonPath('$.data.expense.id');
        $I->seeResponseJsonMatchesJsonPath('$.data.expense.description');
        $I->seeResponseJsonMatchesJsonPath('$.data.expense.category');
        $I->seeResponseJsonMatchesJsonPath('$.data.expense.value');
        $I->seeResponseJsonMatchesJsonPath('$.data.expense.date');

        // Store expense ID for cleanup
        $response = json_decode($I->grabResponse(), true);
        $this->testExpenseIds[] = $response['data']['expense']['id'];

        // Verify expense data
        $I->seeResponseContainsJson([
            'data' => [
                'expense' => [
                    'description' => $expenseData['description'],
                    'value' => $expenseData['value']
                ]
            ]
        ]);
    }

    /**
     * Test creating expense with missing required fields
     */
    public function testCreateExpenseMissingFields(ApiTester $I)
    {
        $I->wantTo('fail creating expense with missing required fields');

        $I->sendPOST('/api/expense', [
            'description' => 'Incomplete expense'
            // Missing category, amount, date
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false
        ]);
    }

    /**
     * Test creating expense with invalid data
     */
    public function testCreateExpenseInvalidData(ApiTester $I)
    {
        $I->wantTo('fail creating expense with invalid data');

        $I->sendPOST('/api/expense', [
            'description' => '',
            'category' => 'invalid',
            'value' => 'not-a-number',
            'date' => 'invalid-date'
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false
        ]);
    }

    /**
     * Test creating expense with negative amount
     */
    public function testCreateExpenseNegativeAmount(ApiTester $I)
    {
        $I->wantTo('fail creating expense with negative amount');

        $expenseData = $I->getTestExpenseData([
            'value' => -10.50
        ]);

        $I->sendPOST('/api/expense', $expenseData);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false
        ]);
    }

    /**
     * Test listing expenses
     */
    public function testListExpensesSuccess(ApiTester $I)
    {
        $I->wantTo('list all expenses');

        // Create a test expense first
        $expenseId = $I->createTestExpense($this->token);

        // Send GET request to list expenses
        $I->sendGET('/api/expense');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->validateExpenseListStructure();

        // Check if created expense appears in list
        $response = json_decode($I->grabResponse(), true);
        $expenses = $response['data']['expenses'];

        $found = false;
        foreach ($expenses as $expense) {
            if ($expense['id'] == $expenseId) {
                $found = true;
                break;
            }
        }
        $I->assertTrue($found, 'Created expense should appear in the list');

        // Clean up manually this specific expense
        $I->sendDELETE("/api/expense/{$expenseId}");
        $I->seeResponseCodeIs(200);
    }

    /**
     * Test listing expenses with pagination
     */
    public function testListExpensesPagination(ApiTester $I)
    {
        $I->wantTo('list expenses with pagination');

        $I->sendGET('/api/expense?page=1&per_page=5');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->validateExpenseListStructure();

        // Check pagination parameters
        $response = json_decode($I->grabResponse(), true);
        $pagination = $response['data']['pagination'];

        $I->assertEquals(1, $pagination['current_page']);
        $I->assertEquals(5, $pagination['per_page']);
        $I->assertTrue($pagination['total_count'] >= 0);
        $I->assertTrue($pagination['total_pages'] >= 1);
    }

    /**
     * Test getting single expense
     */
    public function testGetExpenseSuccess(ApiTester $I)
    {
        $I->wantTo('get a single expense by ID');

        // Create a test expense first
        $expenseId = $I->createTestExpense($this->token, [
            'description' => 'Single expense test'
        ]);
        $this->testExpenseIds[] = $expenseId;

        $I->sendGET("/api/expense/{$expenseId}");

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Success'
        ]);

        $I->validateExpenseStructure();

        // Verify the correct expense is returned
        $I->seeResponseContainsJson([
            'data' => [
                'expense' => [
                    'id' => $expenseId,
                    'description' => 'Single expense test'
                ]
            ]
        ]);
    }

    /**
     * Test getting non-existent expense
     */
    public function testGetExpenseNotFound(ApiTester $I)
    {
        $I->wantTo('fail getting non-existent expense');

        $I->sendGET('/api/expense/99999');

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Expense not found'
        ]);
    }

    /**
     * Test updating expense
     */
    public function testUpdateExpenseSuccess(ApiTester $I)
    {
        $I->wantTo('update an existing expense');

        // Create a test expense first
        $expenseId = $I->createTestExpense($this->token, [
            'description' => 'Original description',
            'value' => 30.00
        ]);
        $this->testExpenseIds[] = $expenseId;

        // Update the expense
        $updatedData = [
            'description' => 'Updated description',
            'category' => 2,
            'value' => 75.50,
            'date' => date('Y-m-d', strtotime('-1 day'))
        ];

        $I->sendPUT("/api/expense/{$expenseId}", $updatedData);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Expense updated successfully'
        ]);

        $I->validateExpenseStructure();

        // Verify updated data
        $I->seeResponseContainsJson([
            'data' => [
                'expense' => [
                    'id' => $expenseId,
                    'description' => $updatedData['description'],
                    'value' => $updatedData['value']
                ]
            ]
        ]);
    }

    /**
     * Test updating non-existent expense
     */
    public function testUpdateExpenseNotFound(ApiTester $I)
    {
        $I->wantTo('fail updating non-existent expense');

        $updatedData = $I->getTestExpenseData([
            'description' => 'This should fail'
        ]);

        $I->sendPUT('/api/expense/99999', $updatedData);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Expense not found'
        ]);
    }

    /**
     * Test updating expense with invalid data
     */
    public function testUpdateExpenseInvalidData(ApiTester $I)
    {
        $I->wantTo('fail updating expense with invalid data');

        // Create a test expense first
        $expenseId = $I->createTestExpense($this->token);
        $this->testExpenseIds[] = $expenseId;

        $I->sendPUT("/api/expense/{$expenseId}", [
            'description' => '',
            'amount' => 'invalid-amount'
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false
        ]);
    }

    /**
     * Test deleting expense
     */
    public function testDeleteExpenseSuccess(ApiTester $I)
    {
        $I->wantTo('delete an existing expense');

        // Create a test expense first
        $expenseId = $I->createTestExpense($this->token, [
            'description' => 'Expense to be deleted'
        ]);

        $I->sendDELETE("/api/expense/{$expenseId}");

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);

        // Verify expense is deleted by trying to get it
        $I->sendGET("/api/expense/{$expenseId}");
        $I->seeResponseCodeIs(404);
    }

    /**
     * Test deleting non-existent expense
     */
    public function testDeleteExpenseNotFound(ApiTester $I)
    {
        $I->wantTo('fail deleting non-existent expense');

        $I->sendDELETE('/api/expense/99999');

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => false,
            'message' => 'Expense not found'
        ]);
    }

    /**
     * Test expense filtering by category
     */
    public function testFilterExpensesByCategory(ApiTester $I)
    {
        $I->wantTo('filter expenses by category');

        // Clean existing expenses for isolation
        $I->sendGET('/api/expense');
        $I->seeResponseCodeIs(200);
        $existingResponse = json_decode($I->grabResponse(), true);

        // Delete existing expenses to ensure test isolation
        if (isset($existingResponse['data']['expenses'])) {
            foreach ($existingResponse['data']['expenses'] as $expense) {
                $I->sendDELETE("/api/expense/{$expense['id']}");
            }
        }

        // Create expenses in different categories
        $expense1Id = $I->createTestExpense($this->token, [
            'description' => 'Food expense',
            'category' => 1
        ]);
        $expense2Id = $I->createTestExpense($this->token, [
            'description' => 'Transport expense',
            'category' => 2
        ]);

        $this->testExpenseIds[] = $expense1Id;
        $this->testExpenseIds[] = $expense2Id;

        // Filter by category 1 (Food)
        $I->sendGET('/api/expense?category=1');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->validateExpenseListStructure();

        // Check that all returned expenses have category 1
        $response = json_decode($I->grabResponse(), true);
        $expenses = $response['data']['expenses'];

        foreach ($expenses as $expense) {
            $I->assertEquals(1, $expense['category'], 'All expenses should be in category 1');
        }
    }

    /**
     * Test expense filtering by date range
     */
    public function testFilterExpensesByDateRange(ApiTester $I)
    {
        $I->wantTo('filter expenses by date range');

        // Clean existing expenses for isolation
        $I->sendGET('/api/expense');
        $I->seeResponseCodeIs(200);
        $existingResponse = json_decode($I->grabResponse(), true);

        // Delete existing expenses to ensure test isolation
        if (isset($existingResponse['data']['expenses'])) {
            foreach ($existingResponse['data']['expenses'] as $expense) {
                $I->sendDELETE("/api/expense/{$expense['id']}");
            }
        }

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Create expenses with different dates
        $expense1Id = $I->createTestExpense($this->token, [
            'description' => 'Today expense',
            'date' => $today
        ]);
        $expense2Id = $I->createTestExpense($this->token, [
            'description' => 'Yesterday expense',
            'date' => $yesterday
        ]);

        $this->testExpenseIds[] = $expense1Id;
        $this->testExpenseIds[] = $expense2Id;

        // Filter by today's date
        $I->sendGET("/api/expense?date_from={$today}&date_to={$today}");

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->validateExpenseListStructure();

        // Check that returned expenses are from today
        $response = json_decode($I->grabResponse(), true);
        $expenses = $response['data']['expenses'];

        foreach ($expenses as $expense) {
            $I->assertEquals($today, $expense['date'], 'All expenses should be from today');
        }
    }

    /**
     * Test unauthorized access (no token)
     */
    public function testUnauthorizedAccess(ApiTester $I)
    {
        $I->wantTo('fail accessing expenses without authentication');

        // Remove authorization header
        $I->deleteHeader('Authorization');

        $I->sendGET('/api/expense');

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Authentication required',
            'status' => 401
        ]);
    }
}
