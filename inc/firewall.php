<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get firewall settings
function yetiwatch_get_firewall_settings() {
    return array(
        'max_attempts' => get_option('yetiwatch_max_login_attempts', 5),
        'time_window' => get_option('yetiwatch_time_window', 3600),
        'block_duration' => get_option('yetiwatch_block_duration', 3600)
    );
}

// Log failed login attempts
function yetiwatch_log_failed_login($username, $ip_address) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] Failed login attempt - Username: $username, IP: $ip_address\n";
    
    if (defined('YETIWATCH_LOG_FILE')) {
        file_put_contents(YETIWATCH_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Handle failed login
function yetiwatch_firewall_login_failed($username) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    yetiwatch_log_failed_login($username, $ip_address);
    
    // Increment attempt count
    yetiwatch_firewall_increment_login_attempt_count($ip_address);
}

// Get the number of login attempts from a specific IP address within a certain time period
function yetiwatch_firewall_get_login_attempt_count($ip_address) {
    $settings = yetiwatch_get_firewall_settings();
    $option_name = 'yetiwatch_login_attempts_' . md5($ip_address);
    $attempts = get_option($option_name, array());
    
    // Clean old attempts
    $current_time = time();
    $attempts = array_filter($attempts, function($timestamp) use ($current_time, $settings) {
        return ($current_time - $timestamp) < $settings['time_window'];
    });
    
    update_option($option_name, $attempts);
    return count($attempts);
}

// Increment the login attempt count for a specific IP address
function yetiwatch_firewall_increment_login_attempt_count($ip_address) {
    $option_name = 'yetiwatch_login_attempts_' . md5($ip_address);
    $attempts = get_option($option_name, array());
    $attempts[] = time();
    update_option($option_name, $attempts);
}

// Block an IP address for a certain duration
function yetiwatch_firewall_block_ip_address($ip_address, $duration) {
    $option_name = 'yetiwatch_blocked_ips';
    $blocked_ips = get_option($option_name, array());
    $blocked_ips[$ip_address] = time() + $duration;
    update_option($option_name, $blocked_ips);
    
    // Log the block
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] IP blocked - IP: $ip_address, Duration: {$duration}s\n";
    if (defined('YETIWATCH_LOG_FILE')) {
        file_put_contents(YETIWATCH_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Check if an IP address is already blocked
function yetiwatch_firewall_is_ip_blocked($ip_address) {
    $option_name = 'yetiwatch_blocked_ips';
    $blocked_ips = get_option($option_name, array());
    
    if (isset($blocked_ips[$ip_address])) {
        if (time() < $blocked_ips[$ip_address]) {
            return true; // Still blocked
        } else {
            // Block expired, remove it
            unset($blocked_ips[$ip_address]);
            update_option($option_name, $blocked_ips);
        }
    }
    return false;
}

// Check login attempts
function yetiwatch_firewall_check_login_attempts() {
    $settings = yetiwatch_get_firewall_settings();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    // Check if the IP address is already blocked
    if (yetiwatch_firewall_is_ip_blocked($ip_address)) {
        // Log the blocked attempt
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] Blocked IP attempted access - IP: $ip_address\n";
        if (defined('YETIWATCH_LOG_FILE')) {
            file_put_contents(YETIWATCH_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
        }
        
        wp_die('Access denied. Your IP has been temporarily blocked due to suspicious activity.', 'Access Denied', array('response' => 403));
    }

    // Check the number of login attempts from the same IP address
    $attempt_count = yetiwatch_firewall_get_login_attempt_count($ip_address);

    // Check if the maximum allowed login attempts have been exceeded
    if ($attempt_count >= $settings['max_attempts']) {
        // Block the IP address
        yetiwatch_firewall_block_ip_address($ip_address, $settings['block_duration']);

        wp_die('Access denied. Too many failed login attempts. Your IP has been temporarily blocked.', 'Access Denied', array('response' => 403));
    }
}

// Placeholder functions (implement as needed)
function yetiwatch_firewall_check_login_username($username) {
    // Implement checks for suspicious usernames or patterns
}

function yetiwatch_firewall_check_login_password($password) {
    // Implement checks for suspicious passwords or patterns
}

function yetiwatch_firewall_check_sql_injection() {
    // Implement checks for potential SQL injection attacks
}

function yetiwatch_firewall_check_xss_attacks() {
    // Implement checks for potential XSS attacks
}

function yetiwatch_firewall_check_admin_activity() {
    // Implement admin activity monitoring
}

function yetiwatch_firewall_logout() {
    // Handle logout event
}

// Firewall initialization function
function yetiwatch_firewall_init() {
    // Add actions and filters to monitor and block suspicious activities
    add_action('wp_login_failed', 'yetiwatch_firewall_login_failed', 10, 1);
    add_action('init', 'yetiwatch_firewall_check_login_attempts', 1);
}

// Call the firewall initialization function
yetiwatch_firewall_init();