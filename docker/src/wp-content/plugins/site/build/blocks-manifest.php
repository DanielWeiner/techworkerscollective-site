<?php
// This file is generated. Do not modify it manually.
return array(
	'logo-link' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'site/logo-link',
		'version' => '0.1.0',
		'title' => 'Logo Link',
		'category' => 'widgets',
		'icon' => '',
		'description' => 'A linkified version of the site logo SVG. Links to the site home page.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'site',
		'editorScript' => 'file:./index.js',
		'render' => 'file:./render.php',
		'attributes' => array(
			'size' => array(
				'type' => 'string',
				'default' => '1.5rem'
			)
		)
	)
);
