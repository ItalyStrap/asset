<?php
/**
 * Assets Interface
 *
 * Handle the CSS and JS regiter and enque
 *
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 *
 * @since 2.0.0
 *
 * @package LocalStrategy\Core
 */

namespace LocalStrategy\Asset;

interface Asset_Interface {

	/**
	 * Checks if an asset has been enqueued
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enqueued();

	/**
	 * Register each of the asset (enqueues it)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function register();

	/**
	 * De-register each of the asset
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	// public function deregister( $handle );
}
