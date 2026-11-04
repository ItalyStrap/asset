<?php

$define_constants = [
	'TEMPLATEURL'				=> 'http://wordpress',
	'STYLESHEETURL'				=> 'http://wordpress',
	'PARENTPATH'				=> \rtrim(\codecept_root_dir(), '/'),
	'CHILDPATH'					=> \rtrim(\codecept_root_dir(), '/'),
	'CURRENT_TEMPLATE_SLUG'		=> 'index',
];

foreach ( $define_constants as $name => $value ) {
	if ( ! defined( $name ) ) {
		define($name, $value);
	}
}
