<?php

$define_constants = [
	'TEMPLATEURL'				=> $_SERVER['TEST_SITE_WP_URL'],
	'STYLESHEETURL'				=> $_SERVER['TEST_SITE_WP_URL'],
	'PARENTPATH'				=> $_SERVER['WP_ROOT_FOLDER'],
	'CHILDPATH'					=> $_SERVER['WP_ROOT_FOLDER'],
	'CURRENT_TEMPLATE_SLUG'		=> 'index',
];

foreach ( $define_constants as $name => $value ) {
	if ( ! defined( $name ) ) {
		define($name, $value);
	}
}
