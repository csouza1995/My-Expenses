<?php

/**
 * Global helper functions for debugging
 * Include this file in your bootstrap to make these functions available globally
 */

use app\helpers\DebugHelper;

if (!function_exists('dd')) {
    /**
     * Dump and die - Laravel style debugging function
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    function dd(...$vars)
    {
        DebugHelper::dd(...$vars);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump without dying - for non-fatal debugging
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    function dump(...$vars)
    {
        DebugHelper::dump(...$vars);
    }
}

if (!function_exists('ddd')) {
    /**
     * Dump, debug backtrace and die - enhanced debugging
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    function ddd(...$vars)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
        echo '<h3>🔬 Enhanced Debug (ddd)</h3>';

        // Adicionar backtrace ao final do array de variáveis
        $vars[] = $backtrace;
        DebugHelper::dd(...$vars);
    }
}

if (!function_exists('ray')) {
    /**
     * Simple ray-like function for quick debugging
     * 
     * @param mixed $var Variable to dump
     * @param string|null $label Optional label
     * @return mixed Returns the variable so it can be chained
     */
    function ray($var, $label = null)
    {
        echo '<div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 10px; margin: 5px 0; font-family: monospace;">';
        if ($label) {
            echo '<strong style="color: #1976d2;">🔍 ' . htmlspecialchars($label) . ':</strong><br>';
        }
        echo '<pre style="margin: 0; font-size: 12px;">' . htmlspecialchars(print_r($var, true)) . '</pre>';
        echo '</div>';

        return $var;
    }
}
