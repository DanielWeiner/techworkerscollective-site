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

add_action( 'init', 'site_register_block_styles');

function site_register_block_styles() {
    register_block_style(
        'core/heading',
        [
            'name'  => 'alt',
            'label' => __( 'Alt', 'site' ),
        ]
    );

    register_block_style(
        'core/heading',
        [
            'name'  => 'sans-serif',
            'label' => __( 'Sans Serif', 'site' ),
        ]
    );

    register_block_style(
        'core/paragraph',
        [
            'name'  => 'alt',
            'label' => __( 'Alt', 'site' ),
        ]
    );
    
    register_block_style(
        'core/paragraph',
        [
            'name'  => 'alt-serif',
            'label' => __( 'Alt Serif', 'site' ),
        ]
    );

    register_block_style(
        'core/columns',
        [
            'name'  => 'dark',
            'label' => __( 'Dark', 'site' ),
        ]
    );

    register_block_style(
        'core/group',
        [
            'name'  => 'banner',
            'label' => __( 'Banner', 'site' ),
        ]
    );

    register_block_style(
        'core/group',
        [
            'name'  => 'dark',
            'label' => __( 'Dark', 'site' ),
        ]
    );
}