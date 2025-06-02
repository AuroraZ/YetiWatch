<?php

// Initialize stealth-related hooks
function yetiwatch_stealth_init() {
    // Remove WordPress version from HTML header
    remove_action('wp_head', 'wp_generator');
    
    // Hide version in RSS feeds
    add_filter('the_generator', '__return_empty_string');
    
    // Optional: You can also hide version in REST API responses
}

// Hide WordPress version for extra stealth
function yetiwatch_remove_wp_version() {
    return '';
}
