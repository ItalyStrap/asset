<?php
/**
 * Asset_Loader API
 *
 * This class load all theme assets enqueued.
 *
 * @author      Enea Overclokk
 * @license     GPL-2.0+
 *
 * @link www.italystrap.com
 * @version 0.0.1-alpha
 *
 * @package ItalyStrap\Asset
 */

namespace ItalyStrap\Asset;

/**
 * Asset_Loader
 */
class Loader {

	public function __construct( array $assets )
	{
		$this->assets = $assets;
	}

	/**
	 * Init script and style
	 */
	public function add_assets() {

		foreach ( $this->assets as $type => $items ) {
			/**
			 * With this hook you can filter the enqueue script and style config
			 * Filters name:
			 * 'italystrap_config_enqueue_style'
			 * 'italystrap_config_enqueue_script'
			 *
			 * @var array
			 */
			$items = apply_filters( 'italystrap_config_enqueue_' . strtolower( $type ) , $items );
			foreach ( $items as $item ) {
				Asset_Factory::make( $item, $type )->register();
			}
		}
	}
}
