<?php

namespace app\helpers;

use yii\helpers\VarDumper;
use Yii;

/**
 * Helper class for debugging functions
 */
class DebugHelper
{
    /**
     * Dump and die - Laravel style debugging function
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    public static function dd(...$vars)
    {
        // Set response headers for better formatting
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }

        echo '<style>
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }
            .dd-container { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
            .dd-header { background: #dc3545; color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; font-weight: bold; }
            .dd-content { padding: 20px; }
            .dd-var { margin-bottom: 20px; }
            .dd-var:last-child { margin-bottom: 0; }
            .dd-trace { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin-top: 20px; }
            .dd-trace h4 { margin: 0 0 10px 0; color: #495057; }
            .dd-trace-item { margin: 5px 0; font-family: monospace; font-size: 12px; }
            .dd-file { color: #007bff; }
            .dd-line { color: #28a745; }
            pre { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; margin: 0; overflow-x: auto; }
        </style>';

        echo '<div class="dd-container">';
        echo '<div class="dd-header">🐛 Debug Output (dd)</div>';
        echo '<div class="dd-content">';

        if (empty($vars)) {
            echo '<div class="dd-var"><strong>No variables to dump</strong></div>';
        } else {
            foreach ($vars as $index => $var) {
                echo '<div class="dd-var">';
                if (count($vars) > 1) {
                    echo '<h4>Variable #' . ($index + 1) . ':</h4>';
                }
                echo '<pre>' . htmlspecialchars(VarDumper::dumpAsString($var, 10, true)) . '</pre>';
                echo '</div>';
            }
        }

        // Add backtrace information
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        if (!empty($trace)) {
            echo '<div class="dd-trace">';
            echo '<h4>📍 Call Stack:</h4>';

            foreach ($trace as $index => $item) {
                if ($index > 5) break; // Limit to first 6 items

                $file = isset($item['file']) ? $item['file'] : 'unknown';
                $line = isset($item['line']) ? $item['line'] : 'unknown';
                $function = isset($item['function']) ? $item['function'] : 'unknown';
                $class = isset($item['class']) ? $item['class'] : '';

                if (strpos($file, 'DebugHelper.php') !== false) continue;

                echo '<div class="dd-trace-item">';
                echo '<span class="dd-file">' . htmlspecialchars(basename($file)) . '</span>';
                echo ':<span class="dd-line">' . $line . '</span>';
                if (!empty($class)) {
                    echo ' - ' . htmlspecialchars($class . '::' . $function . '()');
                } else {
                    echo ' - ' . htmlspecialchars($function . '()');
                }
                echo '</div>';
            }
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';

        // Stop execution
        if (Yii::$app) {
            Yii::$app->end();
        } else {
            exit(1);
        }
    }

    /**
     * Dump without dying - for non-fatal debugging
     * 
     * @param mixed ...$vars Variables to dump
     * @return void
     */
    public static function dump(...$vars)
    {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 10px; margin: 10px 0;">';
        echo '<strong>🔍 Debug Dump:</strong><br>';
        foreach ($vars as $var) {
            echo '<pre style="margin: 5px 0; font-size: 12px;">' . htmlspecialchars(VarDumper::dumpAsString($var, 5, true)) . '</pre>';
        }
        echo '</div>';
    }
}
