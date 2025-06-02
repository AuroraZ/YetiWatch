<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load security modules
require_once plugin_dir_path(__FILE__) . 'inc/firewall.php';
require_once plugin_dir_path(__FILE__) . 'inc/stealth.php';

// Initialize security features
function yetiwatch_security_init() {
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