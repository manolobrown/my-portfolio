<?php
/**
 * Plugin Name: MP Portfolio
 * Description: Custom post types and site-specific functionality.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    register_post_type('project', [
        'labels' => [
            'name' => 'Projects',
            'singular_name' => 'Project',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'projects'],
    ]);
});
