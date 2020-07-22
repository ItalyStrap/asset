<?php
/**
 * Loader Interface
 *
 * @package ItalyStrap\Asset
 */
declare(strict_types=1);

namespace ItalyStrap\Asset;

interface LoaderInterface {

	/**
	 * Handle assets registering and enqueuing
	 *
	 * @return bool
	 */
	public function load( array $assets );
}
