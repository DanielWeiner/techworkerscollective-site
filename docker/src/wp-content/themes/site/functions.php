<?php
/**
 * File: functions.php
 * Style overrides live in style.css; layout overrides in theme.json.
 */

add_action( 'wp_enqueue_scripts', function() {
    // 1. Enqueue parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
    // 2. Enqueue child stylesheet (depends on parent)
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        [ 'parent-style' ],
        wp_get_theme()->get( 'Version' )
    );
} );