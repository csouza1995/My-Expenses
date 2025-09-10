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
    /* Estilos para filtros */
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .category-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        margin: 0.125rem;
        background: #007bff;
        color: white;
        border-radius: 15px;
        cursor: pointer;
        font-size: 0.875rem;
        user-select: none;
        transition: all 0.2s;
    }
    .category-tag:hover {
        background: #0056b3;
        transform: translateY(-1px);
    }
    .category-tag.selected {
        background: #28a745;
    }
    
    /* Filtro de categorias */
    .btn-group.category-filter-container {
        display: flex;
        align-items: center;
        gap: 0;
        flex-wrap: nowrap;
    }
    
    .btn-group.category-filter-container .category-btn-compact {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        margin-left: -1px;
        position: relative;
        user-select: none;
        border-radius: 0;
    }
    
    .btn-group.category-filter-container .category-btn-compact:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
        margin-left: 0;
    }
    
    .btn-group.category-filter-container .category-btn-compact:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    .btn-group.category-filter-container .category-btn-compact:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
        z-index: 2;
    }
    
    .btn-group.category-filter-container .category-btn-compact.active {
        background: #28a745;
        border-color: #28a745;
        color: white;
        font-weight: 600;
        z-index: 3;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
    }
    
    .btn-group.category-filter-container .category-btn-compact:focus {
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        z-index: 3;
    }
    
    .btn-reset-categories {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border: 1px solid #dc3545;
        background: white;
        color: #dc3545;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0.375rem;
        margin-left: 0.5rem;
    }
    
    .btn-reset-categories:hover {
        background: #dc3545;
        color: white;
    }
    
    /* Filtro de período compacto */
    .btn-group.period-filter-container {
        display: flex;
        align-items: center;
        gap: 0;
        flex-wrap: nowrap;
    }
    
    .btn-group.period-filter-container .period-btn-compact {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        margin-left: -1px;
        position: relative;
        border-radius: 0;
    }
    
    .btn-group.period-filter-container .period-btn-compact:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
        margin-left: 0;
    }
    
    .btn-group.period-filter-container .period-btn-compact:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    .btn-group.period-filter-container .period-btn-compact:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
        z-index: 2;
    }
    
    .btn-group.period-filter-container .period-btn-compact.active {
        background: #007bff;
        border-color: #007bff;
        color: white;
        z-index: 3;
        font-weight: 600;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
    }
    
    .btn-group.period-filter-container .period-btn-compact:focus {
        z-index: 3;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Estado personalizado */
    .period-filter-container.custom-mode .period-btn-compact:not(#period-custom) {
        display: none;
    }
    
    .custom-date-info {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        color: #495057;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-right: 0.25rem;
    }
    
    .custom-date-info.show {
        display: flex;
    }
    
    .custom-date-text {
        font-weight: 500;
    }
    
    .custom-date-range {
        color: #007bff;
        font-weight: 600;
    }
    
    .btn-close-custom {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1rem;
        padding: 0;
        margin-left: 0.5rem;
        cursor: pointer;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-close-custom:hover {
        color: #dc3545;
    }
    
    /* Dropdown customizado */
    .custom-date-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .custom-date-content {
        position: absolute;
        right: 0;
        top: 100%;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        padding: 1rem;
        min-width: 280px;
        z-index: 1000;
        margin-top: 0.125rem;
    }
    
    .custom-date-content.show {
        display: block;
    }
    
    .custom-date-content.hide {
        display: none;
    }
    
    .date-input-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .date-input-row:last-child {
        margin-bottom: 0;
    }
    
    .custom-date-input {
        flex: 1;
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .date-label {
        color: #6c757d;
        font-size: 0.875rem;
        min-width: 60px;
        font-weight: 500;
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
    
    // Filtro de categorias
    var selectedCategories = [];
    var clickTimeout = null;
    
    // Aplicar filtro de categorias
    window.applyCategoryFilter = function() {
        var params = new URLSearchParams(window.location.search);
        
        // Remover parâmetros anteriores de categoria
        params.delete('ExpenseSearch[categories]');
        params.delete('ExpenseSearch[categories][]');
        
        // Remover todos os parâmetros de categoria existentes
        var keysToDelete = [];
        for (var key of params.keys()) {
            if (key.startsWith('ExpenseSearch[categories]')) {
                keysToDelete.push(key);
            }
        }
        keysToDelete.forEach(key => params.delete(key));
        
        // Adicionar categorias selecionadas como array
        if (selectedCategories.length > 0) {
            selectedCategories.forEach(function(categoryId) {
                params.append('ExpenseSearch[categories][]', categoryId);
            });
        }
        
        // Recarregar página com filtros
        window.location.search = params.toString();
    }
    
    // Toggle categoria no filtro
    window.toggleCategory = function(categoryId, event) {
        // Prevenir propagação do evento
        event.preventDefault();
        event.stopPropagation();
        
        // Cancelar timeout anterior se existir
        if (clickTimeout) {
            clearTimeout(clickTimeout);
            clickTimeout = null;
            
            // Double click - limpar e adicionar apenas esta categoria
            selectedCategories = [categoryId];
            $('.category-btn-compact').removeClass('active');
            $('#category-' + categoryId).addClass('active');
            applyCategoryFilter();
            return;
        }
        
        // Single click - timeout para verificar se será double click
        clickTimeout = setTimeout(function() {
            clickTimeout = null;
            
            // Single click - toggle categoria
            var index = selectedCategories.indexOf(categoryId);
            var btn = $('#category-' + categoryId);
            
            if (index > -1) {
                // Remover categoria
                selectedCategories.splice(index, 1);
                btn.removeClass('active');
            } else {
                // Adicionar categoria
                selectedCategories.push(categoryId);
                btn.addClass('active');
            }
            
            applyCategoryFilter();
        }, 300); // 300ms para detectar double click
    }
    
    // Resetar filtros de categoria
    window.resetCategoryFilter = function() {
        selectedCategories = [];
        $('.category-btn-compact').removeClass('active');
        applyCategoryFilter();
    }
    
    // Inicializar categorias selecionadas baseado na URL
    window.initializeCategoryFilter = function() {
        var urlParams = new URLSearchParams(window.location.search);
        selectedCategories = [];
        
        // Buscar por parâmetros de array ExpenseSearch[categories][]
        for (var entry of urlParams.entries()) {
            if (entry[0] === 'ExpenseSearch[categories][]') {
                selectedCategories.push(entry[1]);
            }
        }
        
        // Marcar botões ativos
        selectedCategories.forEach(function(categoryId) {
            $('#category-' + categoryId).addClass('active');
        });
    }
    
    // Filtro de período
    window.filterByPeriod = function(period) {
        var dateFrom, dateTo;
        
        // Remove active class from all period buttons
        $('.period-btn-compact').removeClass('active');
        
        // Sair do modo personalizado se estiver ativo
        if ($('.period-filter-container').hasClass('custom-mode')) {
            $('.period-filter-container').removeClass('custom-mode');
            $('.custom-date-info').removeClass('show');
        }
        
        // Função para formatar data como YYYY-MM-DD
        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }
        
        var today = new Date();
        
        switch(period) {
            case 'today':
                dateFrom = dateTo = formatDate(today);
                $('#period-today').addClass('active');
                break;
            case 'yesterday':
                var yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                dateFrom = dateTo = formatDate(yesterday);
                $('#period-yesterday').addClass('active');
                break;
            case 'this_month':
                dateFrom = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                dateTo = formatDate(today);
                $('#period-this-month').addClass('active');
                break;
            case 'last_month':
                var firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                var lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                dateFrom = formatDate(firstDayLastMonth);
                dateTo = formatDate(lastDayLastMonth);
                $('#period-last-month').addClass('active');
                break;
            case '7d':
                var sevenDaysAgo = new Date(today);
                sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
                dateFrom = formatDate(sevenDaysAgo);
                dateTo = formatDate(today);
                $('#period-7d').addClass('active');
                break;
            case '14d':
                var fourteenDaysAgo = new Date(today);
                fourteenDaysAgo.setDate(fourteenDaysAgo.getDate() - 14);
                dateFrom = formatDate(fourteenDaysAgo);
                dateTo = formatDate(today);
                $('#period-14d').addClass('active');
                break;
            case '30d':
                var thirtyDaysAgo = new Date(today);
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                dateFrom = formatDate(thirtyDaysAgo);
                dateTo = formatDate(today);
                $('#period-30d').addClass('active');
                break;
            case 'custom':
                // Para datas customizadas, não alterar os valores dos inputs
                $('#period-custom').addClass('active');
                return;
        }
        
        // Atualizar os inputs de data
        $('#filter-date-from').val(dateFrom);
        $('#filter-date-to').val(dateTo);
        
        // Aplicar filtro
        applyPeriodFilter();
    }
    
    // Aplicar filtro de período
    window.applyPeriodFilter = function() {
        var params = new URLSearchParams(window.location.search);
        
        var dateFrom = $('#filter-date-from').val();
        var dateTo = $('#filter-date-to').val();
        
        // Remover parâmetros anteriores de data
        params.delete('ExpenseSearch[date_from]');
        params.delete('ExpenseSearch[date_to]');
        
        if (dateFrom) params.set('ExpenseSearch[date_from]', dateFrom);
        if (dateTo) params.set('ExpenseSearch[date_to]', dateTo);
        
        // Recarregar página com filtros
        window.location.search = params.toString();
    }
    
    // Controlar dropdown de datas customizadas
    window.toggleCustomDateDropdown = function() {
        var dropdown = $('#custom-date-dropdown');
        dropdown.toggleClass('show hide');
        
        // Se estiver abrindo, marcar como ativo
        if (dropdown.hasClass('show')) {
            $('.period-btn-compact').removeClass('active');
            $('#period-custom').addClass('active');
        }
    }
    
    // Entrar no modo personalizado
    window.enterCustomMode = function() {
        $('.period-filter-container').addClass('custom-mode');
        $('.custom-date-info').addClass('show');
        $('#period-custom').removeClass('active');
        updateCustomDateInfo();
    }
    
    // Sair do modo personalizado
    window.exitCustomMode = function() {
        $('.period-filter-container').removeClass('custom-mode');
        $('.custom-date-info').removeClass('show');
        $('#filter-date-from, #filter-date-to').val('');
        $('.period-btn-compact').removeClass('active');
        applyPeriodFilter();
    }
    
    // Atualizar informações de data personalizada
    window.updateCustomDateInfo = function() {
        var dateFrom = $('#filter-date-from').val();
        var dateTo = $('#filter-date-to').val();
        var infoText = '';
        
        // Função para formatar data string YYYY-MM-DD para DD/MM/YYYY
        function formatDateDisplay(dateString) {
            if (!dateString) return '';
            var parts = dateString.split('-');
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        
        if (dateFrom && dateTo) {
            var fromFormatted = formatDateDisplay(dateFrom);
            var toFormatted = formatDateDisplay(dateTo);
            infoText = fromFormatted + ' até ' + toFormatted;
        } else if (dateFrom) {
            var fromFormatted = formatDateDisplay(dateFrom);
            infoText = 'A partir de ' + fromFormatted;
        } else if (dateTo) {
            var toFormatted = formatDateDisplay(dateTo);
            infoText = 'Até ' + toFormatted;
        } else {
            infoText = 'Selecione as datas';
        }
        
        $('.custom-date-range').text(infoText);
    }
    
    // Fechar dropdown quando clicar fora
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.custom-date-dropdown').length) {
            $('#custom-date-dropdown').removeClass('show').addClass('hide');
        }
    });
    
    // Aplicar filtro de data customizada
    window.applyCustomDateFilter = function() {
        enterCustomMode();
        applyPeriodFilter();
        $('#custom-date-dropdown').removeClass('show').addClass('hide');
    }
    
    // Limpar datas customizadas
    window.clearCustomDates = function() {
        $('#filter-date-from, #filter-date-to').val('');
        $('.period-btn-compact').removeClass('active');
        applyPeriodFilter();
        $('#custom-date-dropdown').removeClass('show').addClass('hide');
    }
    
    // Detectar mudanças nos inputs de data customizados
    $(document).on('change', '#filter-date-from, #filter-date-to', function() {
        updateCustomDateInfo();
    });
    
    // Inicializar filtros baseado na URL
    $(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var dateFrom = urlParams.get('ExpenseSearch[date_from]');
        var dateTo = urlParams.get('ExpenseSearch[date_to]');
        
        if (dateFrom) $('#filter-date-from').val(dateFrom);
        if (dateTo) $('#filter-date-to').val(dateTo);
        
        // Se há datas mas nenhum botão ativo, entrar no modo personalizado
        if ((dateFrom || dateTo) && !$('.period-btn-compact.active').length) {
            enterCustomMode();
        }
        
        // Inicializar dropdown como fechado
        $('#custom-date-dropdown').addClass('hide');
        
        // Inicializar filtro de categorias
        initializeCategoryFilter();
    });
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

    <!-- Filtros -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Filtro de Categorias -->
        <div class="btn-group category-filter-container" role="group">
            <?php foreach ($categories as $id => $name): ?>
                <button type="button"
                    class="btn category-btn-compact"
                    id="category-<?= $id ?>"
                    onclick="toggleCategory('<?= $id ?>', event)"
                    title="Single click: adicionar/remover | Double click: filtrar apenas esta">
                    <?= Html::encode($name) ?>
                </button>
            <?php endforeach; ?>
            <button type="button"
                class="btn btn-reset-categories"
                onclick="resetCategoryFilter()"
                title="Limpar filtros de categoria">
                <i class="fas fa-times"></i> Reset
            </button>
        </div>

        <!-- Filtro de Período -->
        <div class="btn-group period-filter-container" role="group">
            <!-- Informativo de data personalizada -->
            <div class="custom-date-info">
                <span class="custom-date-range">Selecione as datas</span>
                <button type="button" class="btn-close-custom" onclick="exitCustomMode()" title="Sair do modo personalizado">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <button type="button" class="btn period-btn-compact" id="period-today" onclick="filterByPeriod('today')">
                Hoje
            </button>
            <button type="button" class="btn period-btn-compact" id="period-yesterday" onclick="filterByPeriod('yesterday')">
                Ontem
            </button>
            <button type="button" class="btn period-btn-compact" id="period-this-month" onclick="filterByPeriod('this_month')">
                Este Mês
            </button>
            <button type="button" class="btn period-btn-compact" id="period-last-month" onclick="filterByPeriod('last_month')">
                Mês Passado
            </button>
            <button type="button" class="btn period-btn-compact" id="period-7d" onclick="filterByPeriod('7d')">
                7D
            </button>
            <button type="button" class="btn period-btn-compact" id="period-14d" onclick="filterByPeriod('14d')">
                14D
            </button>
            <button type="button" class="btn period-btn-compact" id="period-30d" onclick="filterByPeriod('30d')">
                30D
            </button>

            <!-- Dropdown para datas customizadas -->
            <div class="custom-date-dropdown">
                <button type="button" class="btn period-btn-compact" id="period-custom" onclick="toggleCustomDateDropdown()">
                    <i class="fas fa-calendar"></i>
                </button>

                <div class="custom-date-content hide" id="custom-date-dropdown">
                    <div class="mb-3">
                        <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Período Personalizado</h6>

                        <div class="date-input-row">
                            <span class="date-label">De:</span>
                            <input type="date" class="form-control custom-date-input" id="filter-date-from">
                        </div>

                        <div class="date-input-row">
                            <span class="date-label">Até:</span>
                            <input type="date" class="form-control custom-date-input" id="filter-date-to">
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearCustomDates()">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                            <button type="button" class="btn btn-sm btn-primary flex-fill" onclick="applyCustomDateFilter()">
                                <i class="fas fa-check me-1"></i>Aplicar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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