<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$MEMBER_POST_TYPE = 'member';
$MEMBER_TARGET_ROLE_TAXONOMY = 'member_target_role';
$MEMBER_SKILL_TAXONOMY = 'member_skill';
$MEMBER_CERTIFICATION_TAXONOMY = 'member_certification';

$MEMBER_CUSTOM_FIELDS = [
	'experience_level' => [
		'name' 		  => 'Experience Level',
		'id'   		  => 'experience_level',
		'type' 		  => 'select',
		'placeholder' => 'Select experience level',
		'options'     => [
			'entry'  => 'Entry Level',
			'junior' => 'Junior Level',
			'mid'    => 'Mid Level',
			'senior' => 'Senior Level',
			'staff'  => 'Staff Level',
		]
	],
	'resume' => [
		'name'             => 'Resume',
		'id'               => 'resume',
		'type'             => 'file_upload',
		'force_delete'     => true,
		'max_file_uploads' => 1,
		'mime_type'        => 'application/pdf',
	],
	'target_roles' => [
		'name' 	         => 'Target Roles',
		'id'   	         => 'target_roles',
		'type' 	         => 'taxonomy_advanced',
		'taxonomy'       => $MEMBER_TARGET_ROLE_TAXONOMY,
		'field_type'     => 'select_advanced',
		'add_new'        => true,
		'clone'          => true,
		'remove_default' => true,
		'placeholder'    => 'Select target roles',
	],
	'industry' => [
		'name' 	  => 'Industry',
		'id'   	  => 'industry',
		'type' 	  => 'select',
		'placeholder' => 'Select industry',
		'options'     => [
			'tech'          => 'Technology',
			'finance'       => 'Finance',
			'healthcare'    => 'Healthcare',
			'education'     => 'Education',
			'manufacturing' => 'Manufacturing',
			'agriculture'   => 'Agriculture',
			'arts'  	    => 'Arts & Culture',
			'retail'        => 'Retail',
			'legal'         => 'Legal',
			'hospitality'   => 'Hospitality',
			'consulting'    => 'Consulting',
			'government'    => 'Government',
			'nonprofit'     => 'Nonprofit',
			'media'         => 'Media',
			'other'         => 'Other',
		],
	],
	'skills' => [
		'name' 	         => 'Skills',
		'id'   	         => 'skills',
		'type' 	         => 'taxonomy_advanced',
		'taxonomy'       => $MEMBER_SKILL_TAXONOMY,
		'field_type'     => 'select_advanced',
		'add_new'        => true,
		'clone'          => true,
		'remove_default' => true,
		'placeholder'    => 'Select skills',
	],
	'email' => [
		'name' => 'Email',
		'id'   => 'email',
		'type' => 'email',
	],
	'phone' => [
		'name' => 'Phone',
		'id'   => 'phone',
		'type' => 'text',
	],
	'location' => [
		'name'          => 'Location',
		'id'            => 'location',
		'type'		    => 'text',
	],
	'location_map' => [
		'name'          => 'Location Map',
		'id'            => 'location_map',
		'type' 		    => 'osm',
		'address_field' => 'location',
	],
	'work_preference' => [
		'name'    => 'Work Preference',
		'id'      => 'work_preference',
		'type'    => 'select',
		'options' => [
			'remote'        => 'Remote',
			'on_site'       => 'On-site',
			'hybrid'        => 'Hybrid',
			'no_preference' => 'No Preference',
		],
	],
	'certifications' => [
		'name' 	         => 'Certifications',
		'id'   	         => 'certifications',
		'type' 	         => 'taxonomy_advanced',
		'taxonomy'       => $MEMBER_CERTIFICATION_TAXONOMY,
		'field_type'     => 'select_advanced',
		'add_new'        => true,
		'clone'          => true,
		'remove_default' => true,
		'placeholder'    => 'Select certifications',
	],
	'portfolio_images' => [
		'name'             => 'Portfolio Images',
		'id'               => 'portfolio_images',
		'type'             => 'image_advanced',
		'force_delete'     => true,
		'max_file_uploads' => 5,
	],
	'github' => [
		'name' => 'GitHub',
		'id'   => 'github',
		'type' => 'link',
	],
	'website' => [
		'name' => 'Website',
		'id'   => 'website',
		'type' => 'link',
	],
	'linkedin' => [
		'name' => 'LinkedIn',
		'id'   => 'linkedin',
		'type' => 'link',
	],
];

add_action( 'init', 'site_plugin_register_member_post_type' );
function site_plugin_register_member_post_type() {
	global $MEMBER_POST_TYPE;
	global $MEMBER_TARGET_ROLE_TAXONOMY;
	global $MEMBER_SKILL_TAXONOMY;
	global $MEMBER_CERTIFICATION_TAXONOMY;

    $args = [
        'labels' => [
            'name'          => 'Members',
            'singular_name' => 'Member',
            'menu_name'     => 'Members',
            'add_new'       => 'Add New Member',
            'add_new_item'  => 'Add New Member',
            'new_item'      => 'New Member',
            'edit_item'     => 'Edit Member',
            'view_item'     => 'View Member',
            'all_items'     => 'All Members',
		],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => [
			'title', // Member name
			'editor', // Member bio
			'thumbnail', // Member headshot
			'excerpt', // Member tagline
			'revisions',
			'custom-fields'
		],
	];

    register_post_type($MEMBER_POST_TYPE, $args);
	register_taxonomy($MEMBER_TARGET_ROLE_TAXONOMY, $MEMBER_POST_TYPE, [
		'label'        => 'Target Roles',
		'hierarchical' => false,
		'public'       => true,
		'sort'         => true,
		'labels' => [
			'name'          => 'Target Roles',
			'singular_name' => 'Target Role',
			'menu_name'     => 'Target Roles',
			'add_new_item'  => 'Add New Target Role',
			'new_item_name' => 'New Target Role Name',
		],
	]);

	register_taxonomy($MEMBER_SKILL_TAXONOMY, $MEMBER_POST_TYPE, [
		'label'        => 'Skills',
		'hierarchical' => false,
		'public'       => true,
		'sort'         => true,
		'labels' => [
			'name'          => 'Skills',
			'singular_name' => 'Skill',
			'menu_name'     => 'Skills',
			'add_new_item'  => 'Add New Skill',
			'new_item_name' => 'New Skill Name',
		],
	]);

	register_taxonomy($MEMBER_CERTIFICATION_TAXONOMY, $MEMBER_POST_TYPE, [
		'label'        => 'Certifications',
		'hierarchical' => false,
		'public'       => true,
		'sort'         => true,
		'labels' => [
			'name'          => 'Certifications',
			'singular_name' => 'Certification',
			'menu_name'     => 'Certifications',
			'add_new_item'  => 'Add New Certification',
			'new_item_name' => 'New Certification Name',
		],
	]);
}

add_filter( 'rwmb_meta_boxes', function ( $meta_boxes ) {
	global $MEMBER_POST_TYPE;
	global $MEMBER_CUSTOM_FIELDS;
    $meta_boxes[] = [
        'title'      => 'Member Details',
        'post_types' => $MEMBER_POST_TYPE,
        'fields'     => $MEMBER_CUSTOM_FIELDS,
    ];

    return $meta_boxes;
} );
