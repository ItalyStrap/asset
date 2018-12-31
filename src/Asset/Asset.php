<?php
/**
 * Abstract Asset Class API
 *
 * Handle the CSS and JS regiter and enque
 *
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 *
 * @version 0.0.1-alpha
 *
 * @package ItalyStrap\Asset
 */

namespace ItalyStrap\Asset;

if ( ! defined( 'ABSPATH' ) or ! ABSPATH ) {
	die();
}

use ReflectionClass;
use InvalidArgumentException;
use ItalyStrap\Config\Config_Interface;

/**
 * Class description
 * @todo http://wordpress.stackexchange.com/questions/195864/most-elegant-way-to-enqueue-scripts-in-function-php-with-foreach-loop
 */
abstract class Asset implements Asset_Interface {

	/**
	 * Configuration for the class
	 *
	 * @var Config_Interface
	 */
	protected $config;

	/**
	 *
	 * @var string
	 */
	protected $handle = '';

	/**
	 * The Class name without namespace
	 *
	 * @var string
	 */
	protected $class_name = '';

	/**
	 * Get the default structure.
	 *
	 * @return array
	 */
	abstract protected function get_default_structure();

	abstract protected function deregister( $handle );

	abstract protected function pre_register( array $config = [] );

	abstract protected function enqueue( array $config = [] );

	/**
	 * Asset constructor.
	 * @param Config_Interface $config
	 * @throws \ReflectionException
	 */
	public function __construct( Config_Interface $config ) {

		/**
		 * Credits:
		 * @link https://coderwall.com/p/cpxxxw/php-get-class-name-without-namespace
		 * @php54
		 * $this->class_name =  ( new \ReflectionClass( $this ) )->getShortName();
		 */
		$class_name = new ReflectionClass( $this );
		$this->class_name =  $class_name->getShortName();

		$this->config = $config;
		$this->handle = (string) $config->get( 'handle' );

		$this->validate_asset();
	}

	/**
	 * Register each of the asset (enqueues it)
	 *
	 * @return null
	 */
	public function register() {

		$config = array_merge( $this->get_default_structure(), $this->config->all() );

		if ( isset( $config['deregister'] ) ) {
			$this->deregister($this->handle);
		}

		if ( isset( $config['pre_register'] ) ) {
			$this->pre_register( $config );
			return; // <- This will continue and it wont load the localized object.
		}

		if ( $this->is_load_on( $config ) ) {
			$this->enqueue( $config );
		}

		if ( empty( $config['localize'] ) ) {
			return;
		}

		if ( is_array( $config['localize'] ) ) {
			$this->localize_script( $config );
		}

		return true;
	}

	/**
	 * Optional. Status of the script to check. Default 'enqueued'.
	 * Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
	 *
	 * @return bool
	 */
	private function _is( $list = 'enqueued' ) {
		$func = sprintf( 'wp_%s_is', $this->class_name );
		return (bool) $func( $this->handle, $list );
	}

	/**
	 * Checks if an asset has been enqueued
	 *
	 * @return bool
	 */
	public function is_enqueued() {
		return $this->_is( 'enqueued' );
	}

	/**
	 * Checks if an asset has been registered
	 *
	 * @return bool
	 */
	public function is_registered() {
		return $this->_is( 'registered' );
	}

	/**
	 * Loading asset conditionally.
	 *
	 * @return bool
	 */
	protected function is_load_on( $config ) {

		if ( ! isset( $config['load_on'] ) ) {
			return true;
		}

		/**
		 * Example:
		 * 'load_on'		=> false,
		 * 'load_on'		=> true,
		 * 'load_on'		=> is_my_function\return_bool(),
		 */
		if ( is_bool( $config['load_on'] ) ) {
			return $config['load_on'];
		}

		if ( ! is_string( $config['load_on'] ) ) {
			return true;
		}

		return (bool) call_user_func( $config['load_on'] );
	}

	/**
	 * Validates the asset.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	protected function validate_asset() {
		$message = '';

		if ( ! $this->handle ) {
			$message = __( 'A unique ID is required for the asset.', 'italystrap' );
		}

		if ( $message ) {
			throw new InvalidArgumentException( $message );
		}

		return true;
	}
}
