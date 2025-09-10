<?php

/**
 * Componente de Filtros - Categorias e Período
 * 
 * @var array $categories
 */

use yii\helpers\Html;
?>

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