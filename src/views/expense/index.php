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

// Registrar arquivo JavaScript separado
$this->registerJsFile('@web/js/expense-filters.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Passar URLs e tokens para o JavaScript
$this->registerJs("
    // URLs para as funções JavaScript
    window.expenseCreateUrl = '" . Url::to(['expense/create']) . "';
    window.expenseUpdateUrl = '" . Url::to(['expense/update']) . "';
    window.expenseDeleteUrl = '" . Url::to(['expense/delete']) . "';
    window.csrfParam = '" . Yii::$app->request->csrfParam . "';
    window.csrfToken = '" . Yii::$app->request->csrfToken . "';
");

// Incluir estilos customizados
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

    <!-- Filtros -->
    <?= $this->render('_filters', ['categories' => $categories]) ?>

    <!-- Grid de Despesas -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'description', 'label' => 'Descrição'],
            ['attribute' => 'category', 'label' => 'Categoria', 'value' => function ($model) {
                return $model->getCategoryName();
            }],
            ['attribute' => 'value', 'label' => 'Valor', 'format' => ['currency', 'BRL']],
            ['attribute' => 'date', 'label' => 'Data', 'format' => ['date', 'php:d/m/Y']],
            // Actions
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'header' => 'Ações',
                'options' => ['style' => 'width: 180px;', 'class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center align-middle'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::button(
                            '<i class="fas fa-eye"></i>',
                            [
                                'class' => 'btn btn-sm btn-outline-info btn-action me-1',
                                'title' => 'Ver Mais',
                                'data-bs-toggle' => 'tooltip',
                                'onclick' => "viewExpense('{$model->id}', '" . addslashes($model->description) . "', '{$model->getCategoryName()}', '{$model->value}', '{$model->getDateFormatted()}', '{$model->getCreatedAtFormatted()}', '{$model->getUpdatedAtFormatted()}')"
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
                                'onclick' => "editExpense('{$model->id}', '" . addslashes($model->description) . "', '{$model->category}', '{$model->value}', '{$model->date}')"
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
                                'onclick' => "confirmDelete('{$model->id}', '" . addslashes($model->description) . "')"
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
    ]) ?>

    <!-- Modais -->
    <?= $this->render('_modals', [
        'model' => $model,
        'categories' => $categories
    ]) ?>

</div>