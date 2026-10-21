<?php
/*
 * Plugin Name: Custom Site Plugin
 * Description: Custom plugin for the site.
 * Version: 1.0.0
 * Text Domain: site
 * License: GPL v2 or later
 */

function create_block_logo_link_block_init() {
	wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
}
add_action( 'init', 'create_block_logo_link_block_init' );
