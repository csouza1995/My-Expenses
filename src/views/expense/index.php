<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Search\ExpenseSearch $searchModel */
/** @var app\models\Forms\ExpenseForm $model */
/** @var array $categories */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\bootstrap5\Modal;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Minhas Despesas';

// fontawesome css
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

// make some custom styles
$this->registerCss("
    .expense-card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    .btn-action {
        margin: 0 2px;
        padding: 0.25rem 0.5rem;
    }
    .btn-action i {
        font-size: 12px;
    }
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .required-label::after {
        content: ' *';
        color: #dc3545;
    }
    /* Garantir que os ícones apareçam */
    .fas, .fa {
        font-family: 'Font Awesome 6 Free' !important;
        font-weight: 900 !important;
    }
");

$this->registerJs("
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // reset modal on close
    $('#expense-modal').on('hidden.bs.modal', function () {
        $('#expense-form')[0].reset();
        $('#expense-modal .modal-title').html('<i class=\"fas fa-plus\"></i> Adicionar Despesa');
        $('#expense-form').attr('action', '" . Url::to(['expense/create']) . "');
        $('#expense-id').val('');
        $('#submit-btn').html('<i class=\"fas fa-save\"></i> Salvar').prop('disabled', false);
        
        // clear validation errors
        $('.form-group').removeClass('has-error');
        $('.help-block').remove();
        $('.is-invalid').removeClass('is-invalid');
    });
    
    // reset modal when opening for create
    $('[data-bs-target=\"#expense-modal\"]').on('click', function() {
        $('#expense-modal .modal-title').html('<i class=\"fas fa-plus\"></i> Adicionar Despesa');
        $('#expense-form').attr('action', '" . Url::to(['expense/create']) . "');
        $('#expense-id').val('');
        $('#submit-btn').html('<i class=\"fas fa-save\"></i> Salvar');
    });
    
    // edit expense
    window.editExpense = function(id, description, category, value, date) {
        $('#expense-modal .modal-title').html('<i class=\"fas fa-edit\"></i> Editar Despesa');
        $('#expense-form').attr('action', '" . Url::to(['expense/update']) . "?id=' + id);
        $('#expense-id').val(id);
        $('#expenseform-description').val(description);
        $('#expenseform-category').val(category);
        $('#expenseform-value').val(value);
        $('#expenseform-date').val(date);
        $('#submit-btn').html('<i class=\"fas fa-edit\"></i> Atualizar');
        $('#expense-modal').modal('show');
    }
    
    // view expense
    window.viewExpense = function(id, description, category, value, date, created_at, updated_at) {
        $('#view-description').text(description);
        $('#view-category').text(category);
        $('#view-value').text('R$ ' + parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
        $('#view-date').text(date);        
        $('#view-created').text(created_at);
        $('#view-updated').text(updated_at);
        
        $('#view-modal').modal('show');
    }
    
    // confirm delete
    window.confirmDelete = function(id, description) {
        $('#delete-description').text(description);
        $('#confirm-delete-btn').attr('onclick', 'deleteExpense(' + id + ')');
        $('#delete-modal').modal('show');
    }
    
    // delete expense
    window.deleteExpense = function(id) {
        var form = $('<form>', {
            'method': 'POST',
            'action': '" . Url::to(['expense/delete']) . "?id=' + id
        });
        form.append($('<input>', {
            'type': 'hidden',
            'name': '" . Yii::$app->request->csrfParam . "',
            'value': '" . Yii::$app->request->csrfToken . "'
        }));
        $('body').append(form);
        form.submit();
    }
    
    // prevent multiple submissions
    $('#expense-form').on('submit', function() {
        if (this.checkValidity()) {
            $('#submit-btn').prop('disabled', true).html('<i class=\"fas fa-spinner fa-spin\"></i> Processando...');
        }
    });
    
    // auto hide flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
");
?>
<div class="site-index">
    <div class="py-3 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-wallet"></i> Minhas Despesas</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#expense-modal">
                <i class="fas fa-plus"></i> Adicionar Despesa
            </button>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'description', 'label' => 'Descrição'],
            ['attribute' => 'category', 'label' => 'Categoria', 'value' => function ($model) {
                return $model->getCategoryName();
            }],
            ['attribute' => 'value', 'label' => 'Valor', 'format' => ['currency', 'BRL']],
            ['attribute' => 'date', 'label' => 'Data', 'format' => ['date', 'php:d/m/Y']],
            // actions
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
        'filterModel' => $searchModel,
        'layout' => "{items}\n<div class=\"d-flex justify-content-between\">{summary}{pager}</div>",
    ]) ?>

    <!-- Modal for Add/Edit Expense -->
    <?php Modal::begin([
        'id' => 'expense-modal',
        'title' => '<i class="fas fa-plus"></i> Adicionar Despesa',
        'size' => Modal::SIZE_DEFAULT,
        'options' => ['class' => 'fade'],
        'headerOptions' => ['class' => 'bg-light'],
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'expense-form',
        'action' => ['expense/create'],
        'options' => ['class' => 'needs-validation', 'novalidate' => true],
    ]); ?>

    <?= Html::hiddenInput('id', '', ['id' => 'expense-id']) ?>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <?= $form->field($model, 'description')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Ex: Compra no supermercado',
                    'class' => 'form-control',
                    'required' => true
                ])->label('Descrição', ['class' => 'form-label required-label']) ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <?= $form->field($model, 'category')->dropDownList($categories, [
                    'prompt' => 'Selecione uma categoria...',
                    'class' => 'form-select',
                    'required' => true
                ])->label('Categoria', ['class' => 'form-label required-label']) ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'value')->textInput([
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '0.01',
                    'placeholder' => '0,00',
                    'class' => 'form-control',
                    'required' => true
                ])->label('Valor (R$)', ['class' => 'form-label required-label']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'date')->textInput([
                    'type' => 'date',
                    'value' => date('Y-m-d'),
                    'class' => 'form-control',
                    'required' => true
                ])->label('Data', ['class' => 'form-label required-label']) ?>
            </div>
        </div>

    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <?= Html::submitButton('<i class="fas fa-save"></i> Salvar', [
            'class' => 'btn btn-primary',
            'id' => 'submit-btn'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Modal::end(); ?>

    <!-- Modal de Visualização -->
    <?php Modal::begin([
        'id' => 'view-modal',
        'title' => '<h5><i class="fas fa-eye"></i> Detalhes da Despesa</h5>',
        'size' => Modal::SIZE_DEFAULT,
        'options' => ['class' => 'fade'],
        'headerOptions' => ['class' => 'bg-light'],
    ]); ?>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-4"><strong>Descrição:</strong></div>
            <div class="col-8" id="view-description"></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Categoria:</strong></div>
            <div class="col-8" id="view-category"></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Valor:</strong></div>
            <div class="col-8" id="view-value"></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Data:</strong></div>
            <div class="col-8" id="view-date"></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Criado em:</strong></div>
            <div class="col-8" id="view-created"></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Atualizado em:</strong></div>
            <div class="col-8" id="view-updated"></div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Fechar
        </button>
    </div>

    <?php Modal::end(); ?>

    <!-- Modal de Confirmação de Exclusão -->
    <?php Modal::begin([
        'id' => 'delete-modal',
        'title' => '<h5><i class="fas fa-exclamation-triangle text-warning"></i> Confirmar Exclusão</h5>',
        'size' => Modal::SIZE_DEFAULT,
        'options' => ['class' => 'fade'],
        'headerOptions' => ['class' => 'bg-light'],
    ]); ?>

    <div class="text-center p-3">
        <i class="fas fa-trash fa-3x text-danger mb-3"></i>
        <p class="mb-3">Tem certeza que deseja excluir a despesa:</p>
        <p class="fw-bold" id="delete-description"></p>
        <p class="text-muted small">Esta ação não pode ser desfeita!</p>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="btn btn-danger" id="confirm-delete-btn">
            <i class="fas fa-trash"></i> Sim, Excluir
        </button>
    </div>

    <?php Modal::end(); ?>

</div>