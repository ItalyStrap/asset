<?php
/**
 * Created by PhpStorm.
 * User: fisso
 * Date: 26/12/2018
 * Time: 17:29
 */

use ItalyStrap\Asset;

//add_filter( 'italystrap_config_enqueue_style', function ( array $style ) {
//
//	$min = '.min';
//
//	/**
//	 * Avoid caching script
//	 *
//	 * @var int
//	 */
//	$ver = null;
//
//	$suffix = '.min';
//
//	$dev_dir = '';
//
//	if ( WP_DEBUG ) {
//
//		$ver = rand( 0, 100000 );
//		// $suffix = '';
//		// $dev_dir = 'src/'; // Sistemare il path corretto per i font
//
//	}
//
//	/**
//	 * Only for
//	 * @link http://www.bootstrapcdn.com/alpha/
//	 */
//	$style[] = array(
//		'handle'		=> 'bootstrap',
//		'file'			=> 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha/css/bootstrap.min.css',
//		'deps'			=> null,
//		'version'		=> $ver,
//		'media'			=> null,
//		/**
//		 * With external file you have to preregister the style
//		 */
//		'pre_register'	=> true,
//		/**
//		 * If you want to load only with WP_DEBUG true.
//		 */
//		'load_on'		=> WP_DEBUG, // bool
//	);
//
//	$style[] = array(
//		'handle'	=> CURRENT_TEMPLATE_SLUG,
//		'file'		=>
//			file_exists( STYLESHEETPATH . '/css/' . $dev_dir . CURRENT_TEMPLATE_SLUG . '.css' )
//				? STYLESHEETURL . '/css/' . $dev_dir . CURRENT_TEMPLATE_SLUG . '.css'
//				: STYLESHEETURL . '/css/' . $dev_dir . 'custom.css',
//		// 'deps'		=> array( 'bootstrap' ),
//		'version'	=> $ver,
//		'media'		=> null,
//		// 'load_on'	=> 'is_single',
//	);
//
//	return $style;
//
//}, 10, 1 );
//
//add_filter( 'italystrap_config_enqueue_script', function ( array $script ) {
//
//	$script[] = array(
//		'handle'	=> 'livereload', // Required
//		'file'		=> '//localhost:35729/livereload.js', // Required
//		'in_footer'		=> true,
//		'load_on'		=> WP_DEBUG,
//	);
//
//	$script[] = array(
//		'handle'		=> 'jquery',
//		'file'			=> 'http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
//		'deps'			=> false,
//		'version'		=> $ver,
//		'in_footer'		=> true,
//		'deregister'	=> true, // This will deregister previous registered jQuery.
//		'pre_register'	=> true, // And this will register the new jQuery.
//	);
//
//	$script[] = array(
//		'handle'		=> CURRENT_TEMPLATE_SLUG,
//		'file'			=> file_exists( STYLESHEETPATH . '/js/' . CURRENT_TEMPLATE_SLUG . $min . '.js' ) ? STYLESHEETURL . '/js/' . CURRENT_TEMPLATE_SLUG . $min . '.js' : STYLESHEETURL . '/js/custom' . $min . '.js',
//		'deps'			=> array( 'jquery' ),
//		'version'		=> $ver,
//		'in_footer'		=> true,
//	);
//
//	$script[] = array(
//		/**
//		 * You can call a preregistered script.
//		 */
//		'handle'		=> 'comment-reply',
//		'load_on'		=> 'ItalyStrap\Core\is_comment_reply',
//	);
//
//	return $script;
//}, 10, 1 );

/**
 *
 */
function load_assets() {
//	d( current_filter() );

	$min = '.min';

	/**
	 * Avoid caching script
	 *
	 * @var int
	 */
	$ver = null;

	$suffix = '.min';

	$dev_dir = '';

	if ( SCRIPT_DEBUG ) {

//		$ver = filemtime( 'full/path/of/the/file' );
		// $ver = filemtime($file);
		// $suffix = '';
		// $dev_dir = 'src/'; // Sistemare il path corretto per i font

	}

	$config = [
		'script'	=> [
//			[
//				'handle'		=> 'jquery',
//				'file'			=> '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
//				'deps'			=> false,
//				'version'		=> $ver,
//				'in_footer'		=> true,
//				'pre_register'	=> true,
//				'deregister'	=> true, // This will deregister previous registered jQuery.
//			],
			[
				'handle'		=> 'handle',
				'file'			=> '/js/custom' . $min . '.js',
				'deps'			=> ['jquery'],
				'version'		=> $ver,
				'in_footer'		=> true,
				/**
				 * For now the localize object is set only if the script is not deregister
				 * and if is appendend to the config array of the script to load.
				 */
				'localize'		=> [
					'object_name'	=> 'pluginParams',
					'params'		=> [
						'ajaxurl'		=> admin_url( '/admin-ajax.php' ),
						'ajaxnonce'		=> wp_create_nonce( 'ajaxnonce' ),
						// 'api_endpoint'	=> site_url( '/wp-json/rest/v1/' ),
					],
				],
			],
			[
				'handle'		=> 'comment-reply',
				'load_on'		=> true,
			],
		],
		'style'		=> [
			[
				'handle'	=> 'example_handle',
//				'file'		=> __DIR__ . 'custom.css',
				 'file'			=> 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css',
//				'version'	=> $ver,
				'media'		=> null,
			],
			[
				'handle'	=> 'handle',
				'file'		=> 'custom.css',
				'version'	=> $ver,
				'media'		=> null,
			],
		],
	];

	$loader = new Asset\Loader( $config );

	add_action( 'wp_enqueue_scripts', [ $loader, 'add_assets' ], 10, 1 );
	add_action( 'admin_enqueue_scripts', [ $loader, 'add_assets' ], 10, 1 );
	add_action( 'login_enqueue_scripts', [ $loader, 'add_assets' ], 10, 1 );
}

add_action( 'after_setup_theme', 'load_assets' );
