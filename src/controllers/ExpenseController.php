<?php

namespace app\controllers;

use app\models\Enums\ExpenseCategoriesEnum;
use app\models\Expense;
use app\models\Forms\ExpenseForm;
use app\models\Search\ExpenseSearch;
use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ExpenseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Expense models.
     *
     * @return string
     */
    public function actionIndex()
    {
        // data index
        // $searchModel = new ExpenseSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => Expense::find()->where(['user_id' => Yii::$app->user->id]),
                'pagination' => [
                    'pageSize' => 15,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'created_at' => SORT_DESC,
                    ],
                ],
            ]
        );

        // form
        $model = new ExpenseForm();

        return $this->render('index', [
            'dataProvider' => Yii::$app->user->isGuest ? null : $dataProvider,
            'searchModel' => $searchModel ?? null,

            'model' => $model,
            'categories' => ExpenseCategoriesEnum::getList(),
        ]);
    }

    /**
     * Creates a new Expense model.
     * If creation is successful, redirects to index page.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ExpenseForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $expense = new Expense();
            $expense->description = $model->description;
            $expense->category = $model->category;
            $expense->value = $model->value;
            $expense->date = $model->date;
            $expense->user_id = Yii::$app->user->id;

            if ($expense->save()) {
                Yii::$app->session->setFlash('success', 'Despesa criada com sucesso!');
            } else {
                $errors = $expense->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Erro ao criar despesa: ' . implode(', ', $errors));
            }
        } else {
            $errors = $model->getFirstErrors();
            if (!empty($errors)) {
                Yii::$app->session->setFlash('error', 'Erro de validação: ' . implode(', ', $errors));
            }
        }

        return $this->redirect(['expense/index']);
    }

    /**
     * Updates an existing Expense model.
     * If update is successful, redirects to index page.
     *
     * @param int $id ID
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id = null)
    {
        if ($id === null) {
            $id = Yii::$app->request->get('id');
        }

        $expense = Expense::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if ($expense === null) {
            Yii::$app->session->setFlash('error', 'Despesa não encontrada.');
            return $this->redirect(['expense/index']);
        }

        $model = new ExpenseForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $expense->description = $model->description;
            $expense->category = $model->category;
            $expense->value = $model->value;
            $expense->date = $model->date;

            if ($expense->save()) {
                Yii::$app->session->setFlash('success', 'Despesa atualizada com sucesso!');
            } else {
                $errors = $expense->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Erro ao atualizar despesa: ' . implode(', ', $errors));
            }
        } else {
            $errors = $model->getFirstErrors();
            if (!empty($errors)) {
                Yii::$app->session->setFlash('error', 'Erro de validação: ' . implode(', ', $errors));
            }
        }

        return $this->redirect(['expense/index']);
    }

    /**
     * Deletes an existing Expense model.
     * If deletion is successful, redirects to index page.
     *
     * @param int $id ID
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $expense = Expense::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if ($expense === null) {
            Yii::$app->session->setFlash('error', 'Despesa não encontrada.');
        } else {
            if ($expense->delete()) {
                Yii::$app->session->setFlash('success', 'Despesa excluída com sucesso!');
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao excluir despesa.');
            }
        }

        return $this->redirect(['expense/index']);
    }
}
