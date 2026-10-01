<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$MEMBER_POST_TYPE = 'member';
$MEMBER_CUSTOM_FIELDS = array(
	'experience_level' => 'experience_level'
);

add_action( 'init', 'site_plugin_register_member_post_type' );
function site_plugin_register_member_post_type() {
	global $MEMBER_POST_TYPE;

    $args = array(
        'labels' => array(
            'name'          => 'Members',
            'singular_name' => 'Member',
            'menu_name'     => 'Members',
            'add_new'       => 'Add New Member',
            'add_new_item'  => 'Add New Member',
            'new_item'      => 'New Member',
            'edit_item'     => 'Edit Member',
            'view_item'     => 'View Member',
            'all_items'     => 'All Members',
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array(
			'title', // Member name
			'editor', // Member bio
			'thumbnail', // Member headshot
			'excerpt', // Member tagline
			'revisions',
			'custom-fields'
		),
    );

    register_post_type( $MEMBER_POST_TYPE, $args );
}

add_filter( 'postmeta_form_keys', 'site_plugin_add_member_experience_level', 10, 2 );
function site_plugin_add_member_experience_level( $keys, $post ) {
	global $MEMBER_POST_TYPE;
	global $MEMBER_CUSTOM_FIELDS;

    if ( $post->post_type === $MEMBER_POST_TYPE ) {
        $keys[] = $MEMBER_CUSTOM_FIELDS['experience_level'];
    }

    return $keys;
}
