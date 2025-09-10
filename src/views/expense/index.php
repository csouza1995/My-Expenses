<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Search\ExpenseSearch $searchModel */
/** @var app\models\Forms\ExpenseForm $model */
/** @var array $categories */

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;

$this->title = 'Minhas Despesas';

// FontAwesome CSS
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

// Register separate JavaScript file
$this->registerJsFile('@web/js/expense-filters.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Pass URLs and tokens to JavaScript
$this->registerJs("
    // URLs for JavaScript functions
    window.expenseCreateUrl = '" . Url::to(['expense/create']) . "';
    window.expenseUpdateUrl = '" . Url::to(['expense/update']) . "';
    window.expenseDeleteUrl = '" . Url::to(['expense/delete']) . "';
    window.csrfParam = '" . Yii::$app->request->csrfParam . "';
    window.csrfToken = '" . Yii::$app->request->csrfToken . "';
");

// Include custom styles
echo $this->render('_styles');
?>

<div class="site-index">
    <!-- Header -->
    <div class="py-3 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-wallet"></i> Minhas Despesas</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#expense-modal">
                <i class="fas fa-plus"></i> Adicionar Despesa
            </button>
        </div>
    </div>

    <!-- Filters -->
    <?= $this->render('_filters', ['categories' => $categories]) ?>

    <!-- Expenses Grid -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'description', 'label' => 'Descrição'],
            ['attribute' => 'category', 'label' => 'Categoria', 'value' => function ($model) {
                return $model->getCategoryName();
            }],
            [
                'attribute' => 'value',
                'label' => 'Valor',
                'value' => function ($model) {
                    return 'R$ ' . number_format($model->value, 2, ',', '.');
                },
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 120px;'],
                'contentOptions' => ['class' => 'text-center align-middle']
            ],
            [
                'attribute' => 'date',
                'label' => 'Data',
                'format' => ['date', 'php:d/m/Y'],
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 100px;'],
                'contentOptions' => ['class' => 'text-center align-middle']
            ],
            // Actions
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'header' => 'Ações',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 140px;'],
                'contentOptions' => ['class' => 'text-center align-middle'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::button(
                            '<i class="fas fa-eye"></i>',
                            [
                                'class' => 'btn btn-sm btn-outline-info btn-action me-1',
                                'title' => 'Ver Mais',
                                'data-bs-toggle' => 'tooltip',
                                'onclick' => "viewExpense(" . (int)$model->id . ", '" . Html::encode(addslashes($model->description)) . "', '" . Html::encode($model->getCategoryName()) . "', '" . Html::encode($model->value) . "', '" . Html::encode($model->getDateFormatted()) . "', '" . Html::encode($model->getCreatedAtFormatted()) . "', '" . Html::encode($model->getUpdatedAtFormatted()) . "')"
                            ]
                        );
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::button(
                            '<i class="fas fa-edit"></i>',
                            [
                                'class' => 'btn btn-sm btn-outline-primary btn-action me-1',
                                'title' => 'Editar Despesa',
                                'data-bs-toggle' => 'tooltip',
                                'onclick' => "editExpense(" . (int)$model->id . ", '" . Html::encode(addslashes($model->description)) . "', " . (int)$model->category . ", '" . Html::encode($model->value) . "', '" . Html::encode($model->date) . "')"
                            ]
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::button(
                            '<i class="fas fa-trash"></i>',
                            [
                                'class' => 'btn btn-sm btn-outline-danger btn-action',
                                'title' => 'Excluir Despesa',
                                'data-bs-toggle' => 'tooltip',
                                'onclick' => "confirmDelete(" . (int)$model->id . ", '" . Html::encode(addslashes($model->description)) . "')"
                            ]
                        );
                    },
                ],
            ],
        ],
        'pager' => [
            'class' => LinkPager::class,
            'pagination' => $dataProvider->pagination,
        ],
        'layout' => "{items}\n<div class=\"d-flex justify-content-between\">{summary}{pager}</div>",
        'summaryOptions' => ['class' => 'summary text-muted'],
        'summary' => 'Exibindo {begin}-{end} de {totalCount} itens',
    ]) ?>

    <!-- Modals -->
    <?= $this->render('_modals', [
        'model' => $model,
        'categories' => $categories
    ]) ?>

</div>