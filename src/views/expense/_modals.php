<?php

/**
 * Modais da aplicação - Adicionar/Editar, Visualizar e Excluir
 * 
 * @var app\models\Forms\ExpenseForm $model
 * @var array $categories
 */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
?>

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