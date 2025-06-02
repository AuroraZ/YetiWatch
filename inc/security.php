<?php
if (!defined('ABSPATH')) exit;

add_action('wp_login_failed', 'yetiwatch_log_failed_login');

function yetiwatch_log_failed_login($username) {
    if (get_option('yetiwatch_enable_protection', 'no') !== 'yes') return;

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $time = date('Y-m-d H:i:s');
    $log_line = "[$time] IP: $ip Username: $username" . PHP_EOL;

    // Ensure log directory exists
    if (!file_exists(YETIWATCH_LOG_DIR)) {
        mkdir(YETIWATCH_LOG_DIR, 0750, true);
    }

    // Create log file if missing
    if (!file_exists(YETIWATCH_LOG_FILE)) {
        file_put_contents(YETIWATCH_LOG_FILE, '');
        chmod(YETIWATCH_LOG_FILE, 0640);
    }

    // Append log line safely
    file_put_contents(YETIWATCH_LOG_FILE, $log_line, FILE_APPEND | LOCK_EX);
}
