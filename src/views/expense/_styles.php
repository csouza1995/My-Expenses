<?php

/**
 * Custom CSS styles for expenses view
 */

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
