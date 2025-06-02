<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load security modules
require_once plugin_dir_path(__FILE__) . 'inc/firewall.php';
require_once plugin_dir_path(__FILE__) . 'inc/stealth.php';

// Test logging function (remove after testing)
function yetiwatch_test_logging() {
    $log_message = date('Y-m-d H:i:s') . " - Test log entry from YetiWatch\n";
    
    if (file_exists(YETIWATCH_LOG_FILE)) {
        file_put_contents(YETIWATCH_LOG_FILE, $log_message, FILE_APPEND | LOCK_EX);
        error_log("YetiWatch: Test log written to " . YETIWATCH_LOG_FILE);
    } else {
        error_log("YetiWatch: Log file does not exist at " . YETIWATCH_LOG_FILE);
    }
}

// Initialize security features
function yetiwatch_security_init() {
    // Test logging on initialization
    yetiwatch_test_logging();
    
    // Check if firewall is enabled
    if (get_option('yetiwatch_firewall_enabled', 1)) {
        yetiwatch_firewall_init();
    }
    
    // Check if stealth mode is enabled
    if (get_option('yetiwatch_stealth_enabled', 1)) {
        yetiwatch_stealth_init();
    }
}

// Hook into WordPress
add_action('plugins_loaded', 'yetiwatch_security_init');