<?php
/*
Plugin Name: YetiWatch
Description: Basic security monitoring for WordPress.
Version: 1.0
Author: Shawn Woodbury (YetiNode/CryptidSecurity)
License: MIT
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!defined('ABSPATH')) exit;

// Define constants
define('YETIWATCH_LOG_DIR', plugin_dir_path(__FILE__) . 'logs/');
define('YETIWATCH_LOG_FILE', YETIWATCH_LOG_DIR . 'failed_logins.log');

// Create log directory and file if missing
register_activation_hook(__FILE__, function () {
    if (!file_exists(YETIWATCH_LOG_DIR)) {
        mkdir(YETIWATCH_LOG_DIR, 0750, true);
    }
    if (!file_exists(YETIWATCH_LOG_FILE)) {
        file_put_contents(YETIWATCH_LOG_FILE, '');
        chmod(YETIWATCH_LOG_FILE, 0640);
    }
});

// Load admin and core
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin.php';
}
require_once plugin_dir_path(__FILE__) . 'security.php';
