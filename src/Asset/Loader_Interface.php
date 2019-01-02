<?php
/**
 * Loader Interface
 *
 * @package ItalyStrap\Asset
 */

namespace ItalyStrap\Asset;

interface Loader_Interface {

	/**
	 * Handle assets registering and enqueuing
	 *
	 * @return bool
	 */
	public function run( array $assets );
}
