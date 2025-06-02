<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
add_action('admin_menu', 'yetiwatch_admin_menu');

// Add settings link to plugin page
add_filter('plugin_action_links_' . plugin_basename(dirname(__FILE__) . '/yetiwatch.php'), 'yetiwatch_plugin_action_links');

function yetiwatch_admin_menu() {
    add_options_page(
        'YetiWatch Settings',
        'YetiWatch',
        'manage_options',
        'yetiwatch-settings',
        'yetiwatch_settings_page'
    );
}

function yetiwatch_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=yetiwatch-settings') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

function yetiwatch_settings_page() {
    // Handle form submission
    if (isset($_POST['submit'])) {
        check_admin_referer('yetiwatch_settings_nonce');
        
        update_option('yetiwatch_firewall_enabled', isset($_POST['firewall_enabled']) ? 1 : 0);
        update_option('yetiwatch_stealth_enabled', isset($_POST['stealth_enabled']) ? 1 : 0);
        update_option('yetiwatch_max_login_attempts', intval($_POST['max_login_attempts']));
        update_option('yetiwatch_block_duration', intval($_POST['block_duration']));
        update_option('yetiwatch_time_window', intval($_POST['time_window']));
        
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    // Get current settings
    $firewall_enabled = get_option('yetiwatch_firewall_enabled', 1);
    $stealth_enabled = get_option('yetiwatch_stealth_enabled', 1);
    $max_attempts = get_option('yetiwatch_max_login_attempts', 5);
    $block_duration = get_option('yetiwatch_block_duration', 3600);
    $time_window = get_option('yetiwatch_time_window', 3600);
    
    // Get log file info
    $log_file_exists = file_exists(YETIWATCH_LOG_FILE);
    $log_file_size = $log_file_exists ? filesize(YETIWATCH_LOG_FILE) : 0;
    $log_entries = 0;
    
    if ($log_file_exists && $log_file_size > 0) {
        $log_content = file_get_contents(YETIWATCH_LOG_FILE);
        $log_entries = substr_count($log_content, "\n");
    }
    
    ?>
    <div class="wrap">
        <h1>YetiWatch Security Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('yetiwatch_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Firewall Protection</th>
                    <td>
                        <label>
                            <input type="checkbox" name="firewall_enabled" value="1" <?php checked($firewall_enabled, 1); ?>>
                            Enable firewall protection (login attempt monitoring and IP blocking)
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Stealth Mode</th>
                    <td>
                        <label>
                            <input type="checkbox" name="stealth_enabled" value="1" <?php checked($stealth_enabled, 1); ?>>
                            Enable stealth mode (hide WordPress version and other identifying information)
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Max Login Attempts</th>
                    <td>
                        <input type="number" name="max_login_attempts" value="<?php echo esc_attr($max_attempts); ?>" min="1" max="20">
                        <p class="description">Maximum failed login attempts before blocking IP address</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Time Window (seconds)</th>
                    <td>
                        <input type="number" name="time_window" value="<?php echo esc_attr($time_window); ?>" min="300" max="86400">
                        <p class="description">Time window for counting login attempts (default: 3600 = 1 hour)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Block Duration (seconds)</th>
                    <td>
                        <input type="number" name="block_duration" value="<?php echo esc_attr($block_duration); ?>" min="300" max="86400">
                        <p class="description">How long to block IP addresses (default: 3600 = 1 hour)</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <h2>Security Status</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Firewall Status</th>
                <td>
                    <span class="<?php echo $firewall_enabled ? 'yetiwatch-status-enabled' : 'yetiwatch-status-disabled'; ?>">
                        <?php echo $firewall_enabled ? 'Enabled' : 'Disabled'; ?>
                    </span>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Stealth Mode Status</th>
                <td>
                    <span class="<?php echo $stealth_enabled ? 'yetiwatch-status-enabled' : 'yetiwatch-status-disabled'; ?>">
                        <?php echo $stealth_enabled ? 'Enabled' : 'Disabled'; ?>
                    </span>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Log File</th>
                <td>
                    <?php if ($log_file_exists): ?>
                        <span class="yetiwatch-status-enabled">Active</span>
                        <p class="description">
                            Size: <?php echo size_format($log_file_size); ?> | 
                            Entries: <?php echo $log_entries; ?>
                            <?php if ($log_entries > 0): ?>
                                | <a href="<?php echo admin_url('options-general.php?page=yetiwatch-settings&action=view_log'); ?>">View Log</a>
                                | <a href="<?php echo admin_url('options-general.php?page=yetiwatch-settings&action=clear_log'); ?>" onclick="return confirm('Are you sure you want to clear the log?')">Clear Log</a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <span class="yetiwatch-status-disabled">Log file not found</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php
        // Handle log actions
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'view_log' && $log_file_exists) {
                echo '<h2>Recent Log Entries</h2>';
                echo '<div style="background: #f1f1f1; padding: 10px; border: 1px solid #ccc; max-height: 400px; overflow-y: scroll;">';
                echo '<pre>' . esc_html(file_get_contents(YETIWATCH_LOG_FILE)) . '</pre>';
                echo '</div>';
            } elseif ($_GET['action'] === 'clear_log' && $log_file_exists) {
                file_put_contents(YETIWATCH_LOG_FILE, '');
                echo '<div class="notice notice-success"><p>Log cleared successfully!</p></div>';
            }
        }
        ?>
        
        <style>
        .yetiwatch-status-enabled {
            color: #46b450;
            font-weight: bold;
        }
        .yetiwatch-status-disabled {
            color: #dc3232;
            font-weight: bold;
        }
        </style>
    </div>
    <?php
}
