<?php

namespace app\controllers\api;

use Yii;
use app\models\Entities\Expense;
use app\models\Forms\Api\ExpenseForm;
use app\models\Search\ExpenseSearch;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

/**
 * API Expense Controller
 * Handles expense CRUD operations with JWT authentication
 */
class ExpenseController extends BaseApiController
{
    protected $currentUser;

    public function init()
    {
        parent::init();

        // Verify JWT token for all actions
        try {
            $this->currentUser = $this->verifyJwtToken();
        } catch (\Exception $e) {
            throw new \yii\web\UnauthorizedHttpException('Authentication required');
        }
    }

    /**
     * List expenses with filters
     * GET /api/expense
     */
    public function actionIndex()
    {
        $searchModel = new ExpenseSearch();
        $params = Yii::$app->request->getQueryParams();

        // Override user_id to ensure user can only see their own expenses
        $params['user_id'] = $this->currentUser->id;
        $dataProvider = $searchModel->search($params);

        $expenses = [];
        foreach ($dataProvider->getModels() as $expense) {
            $expenses[] = $this->formatExpenseOutput($expense);
        }

        $pagination = $dataProvider->getPagination();

        return $this->successResponse([
            'expenses' => $expenses,
            'pagination' => [
                'current_page' => ($pagination->offset / $pagination->limit) + 1,
                'per_page' => $pagination->limit,
                'total_count' => $dataProvider->getTotalCount(),
                'total_pages' => max(1, ceil($dataProvider->getTotalCount() / $pagination->limit))
            ]
        ]);
    }

    /**
     * Get single expense
     * GET /api/expense/{id}
     */
    public function actionView($id)
    {
        try {
            $expense = $this->findExpense($id);

            return $this->successResponse([
                'expense' => $this->formatExpenseOutput($expense)
            ]);
        } catch (\yii\web\NotFoundHttpException $e) {
            return $this->errorResponse('Expense not found', null, 404);
        }
    }

    /**
     * Create new expense
     * POST /api/expense
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->getBodyParams();

        $form = new ExpenseForm();

        if (!$form->load($data, '') || !$form->validate()) {
            return $this->errorResponse('Validation failed', $form->errors, 422);
        }

        $expense = new Expense();
        $expense->description = $form->description;
        $expense->category = $form->category;
        $expense->value = $form->value;
        $expense->date = $form->date;
        $expense->user_id = $this->currentUser->id;

        if (!$expense->save()) {
            return $this->errorResponse('Failed to create expense', $expense->errors, 500);
        }

        return $this->successResponse([
            'expense' => $this->formatExpenseOutput($expense)
        ], 'Expense created successfully');
    }

    /**
     * Update expense
     * PUT /api/expense/{id}
     */
    public function actionUpdate($id)
    {
        try {
            $expense = $this->findExpense($id);
            $data = Yii::$app->request->getBodyParams();

            $form = new ExpenseForm();

            if (!$form->load($data, '') || !$form->validate()) {
                return $this->errorResponse('Validation failed', $form->errors, 422);
            }

            $expense->description = $form->description;
            $expense->category = $form->category;
            $expense->value = $form->value;
            $expense->date = $form->date;

            if (!$expense->save()) {
                return $this->errorResponse('Failed to update expense', $expense->errors, 500);
            }

            return $this->successResponse([
                'expense' => $this->formatExpenseOutput($expense)
            ], 'Expense updated successfully');
        } catch (\yii\web\NotFoundHttpException $e) {
            return $this->errorResponse('Expense not found', null, 404);
        }
    }

    /**
     * Delete expense
     * DELETE /api/expense/{id}
     */
    public function actionDelete($id)
    {
        try {
            $expense = $this->findExpense($id);

            if (!$expense->delete()) {
                return $this->errorResponse('Failed to delete expense', null, 500);
            }

            return $this->successResponse(null, 'Expense deleted successfully');
        } catch (\yii\web\NotFoundHttpException $e) {
            return $this->errorResponse('Expense not found', null, 404);
        }
    }

    /**
     * Find expense by ID ensuring user ownership
     */
    protected function findExpense($id)
    {
        // Validate ID
        if (!is_numeric($id) || (int)$id != $id || (int)$id <= 0) {
            throw new BadRequestHttpException('Invalid expense ID');
        }

        $expense = Expense::findOne([
            'id' => (int)$id,
            'user_id' => $this->currentUser->id
        ]);

        if (!$expense) {
            throw new NotFoundHttpException('Expense not found');
        }

        return $expense;
    }

    /**
     * Format expense for API output
     */
    protected function formatExpenseOutput($expense)
    {
        return [
            'id' => (int)$expense->id,
            'description' => $expense->description,
            'category' => (int)$expense->category,
            'category_name' => $expense->getCategoryName(),
            'value' => (float)$expense->value,
            'date' => $expense->date,
            'date_formatted' => $expense->getDateFormatted(),
            'created_at' => $expense->created_at,
            'updated_at' => $expense->updated_at
        ];
    }
}
