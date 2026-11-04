<?php

$define_constants = [
	'TEMPLATEURL'				=> 'http://wordpress',
	'STYLESHEETURL'				=> 'http://wordpress',
	'PARENTPATH'				=> $_SERVER['WP_ROOT_FOLDER'],
	'CHILDPATH'					=> $_SERVER['WP_ROOT_FOLDER'],
	'CURRENT_TEMPLATE_SLUG'		=> 'index',
];

foreach ( $define_constants as $name => $value ) {
	if ( ! defined( $name ) ) {
		define($name, $value);
	}
}
