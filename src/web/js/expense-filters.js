/**
 * JavaScript scripts for expense functionalities
 * Filters, modals and interface interactions
 */

$(document).ready(function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Reset modal on close
    $('#expense-modal').on('hidden.bs.modal', function () {
        $('#expense-form')[0].reset();
        $('#expense-modal .modal-title').html('<i class="fas fa-plus"></i> Adicionar Despesa');
        $('#expense-form').attr('action', window.expenseCreateUrl);
        $('#expense-id').val('');
        $('#submit-btn').html('<i class="fas fa-save"></i> Salvar').prop('disabled', false);
        
        // Clear validation errors
        $('.form-group').removeClass('has-error');
        $('.help-block').remove();
        $('.is-invalid').removeClass('is-invalid');
    });

    // Reset modal when opening for create
    $('[data-bs-target="#expense-modal"]').on('click', function() {
        $('#expense-modal .modal-title').html('<i class="fas fa-plus"></i> Adicionar Despesa');
        $('#expense-form').attr('action', window.expenseCreateUrl);
        $('#expense-id').val('');
        $('#submit-btn').html('<i class="fas fa-save"></i> Salvar');
    });

    // Prevent multiple submissions
    $('#expense-form').on('submit', function() {
        if (this.checkValidity()) {
            $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        }
    });

    // Auto hide flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Detect changes in custom date inputs
    $(document).on('change', '#filter-date-from, #filter-date-to', function() {
        updateCustomDateInfo();
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.custom-date-dropdown').length) {
            $('#custom-date-dropdown').removeClass('show').addClass('hide');
        }
    });

    // Initialize filters
    initializeFilters();
});

// Edit expense function
window.editExpense = function(id, description, category, value, date) {
    // Validate and sanitize inputs
    if (!id || !Number.isInteger(Number(id)) || Number(id) <= 0) {
        alert('ID de despesa inválido.');
        return;
    }
    
    $('#expense-modal .modal-title').html('<i class="fas fa-edit"></i> Editar Despesa');
    $('#expense-form').attr('action', window.expenseUpdateUrl + '?id=' + encodeURIComponent(id));
    $('#expense-id').val(id);
    $('#expenseform-description').val(description);
    $('#expenseform-category').val(category);
    $('#expenseform-value').val(value);
    $('#expenseform-date').val(date);
    $('#submit-btn').html('<i class="fas fa-edit"></i> Atualizar');
    $('#expense-modal').modal('show');
}

// View expense function
window.viewExpense = function(id, description, category, value, date, created_at, updated_at) {
    $('#view-description').text(description);
    $('#view-category').text(category);
    $('#view-value').text('R$ ' + parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    $('#view-date').text(date);        
    $('#view-created').text(created_at);
    $('#view-updated').text(updated_at);
    
    $('#view-modal').modal('show');
}

// Confirm delete function
window.confirmDelete = function(id, description) {
    // Validate ID
    if (!id || !Number.isInteger(Number(id)) || Number(id) <= 0) {
        alert('ID de despesa inválido.');
        return;
    }
    
    // Sanitize description for display
    $('#delete-description').text(description || 'Despesa sem descrição');
    $('#confirm-delete-btn').attr('onclick', 'deleteExpense(' + encodeURIComponent(id) + ')');
    $('#delete-modal').modal('show');
}

// Delete expense function
window.deleteExpense = function(id) {
    // Validate ID before making request
    if (!id || !Number.isInteger(Number(id)) || Number(id) <= 0) {
        alert('ID de despesa inválido.');
        return;
    }
    
    var form = $('<form>', {
        'method': 'POST',
        'action': window.expenseDeleteUrl + '?id=' + encodeURIComponent(id)
    });
    form.append($('<input>', {
        'type': 'hidden',
        'name': window.csrfParam,
        'value': window.csrfToken
    }));
    $('body').append(form);
    form.submit();
}

// Auto hide flash messages after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// ===== FILTERS =====

// Category filter
var selectedCategories = [];
var clickTimeout = null;

// Apply category filter
window.applyCategoryFilter = function() {
    var params = new URLSearchParams(window.location.search);
    
    // Remove previous category parameters
    params.delete('ExpenseSearch[categories]');
    params.delete('ExpenseSearch[categories][]');
    
    // Remove all existing category parameters
    var keysToDelete = [];
    for (var key of params.keys()) {
        if (key.startsWith('ExpenseSearch[categories]')) {
            keysToDelete.push(key);
        }
    }
    keysToDelete.forEach(key => params.delete(key));
    
    // Add selected categories as array
    if (selectedCategories.length > 0) {
        selectedCategories.forEach(function(categoryId) {
            params.append('ExpenseSearch[categories][]', categoryId);
        });
    }
    
    // Reload page with filters
    window.location.search = params.toString();
}

// Toggle category in filter
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

// ===== PERIOD FILTER =====

// Period filter
window.filterByPeriod = function(period) {
    var dateFrom, dateTo;
    
    // Remove active class from all period buttons
    $('.period-btn-compact').removeClass('active');
    
    // Exit custom mode if active
    if ($('.period-filter-container').hasClass('custom-mode')) {
        $('.period-filter-container').removeClass('custom-mode');
        $('.custom-date-info').removeClass('show');
    }
    
    // Function to format date as YYYY-MM-DD
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

// Apply period filter
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

// Enter custom mode
window.enterCustomMode = function() {
    $('.period-filter-container').addClass('custom-mode');
    $('.custom-date-info').addClass('show');
    $('#period-custom').removeClass('active');
    updateCustomDateInfo();
}

// Exit custom mode
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
    
    // Function to format date string YYYY-MM-DD to DD/MM/YYYY
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

// ===== INITIALIZATION =====

// Function to initialize filters based on URL
function initializeFilters() {
    var urlParams = new URLSearchParams(window.location.search);
    var dateFrom = urlParams.get('ExpenseSearch[date_from]');
    var dateTo = urlParams.get('ExpenseSearch[date_to]');
    
    if (dateFrom) $('#filter-date-from').val(dateFrom);
    if (dateTo) $('#filter-date-to').val(dateTo);
    
    // If there are dates but no active button, enter custom mode
    if ((dateFrom || dateTo) && !$('.period-btn-compact.active').length) {
        enterCustomMode();
    }
    
    // Initialize dropdown as closed
    $('#custom-date-dropdown').addClass('hide');
    
    // Initialize category filter
    initializeCategoryFilter();
}
