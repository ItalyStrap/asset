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
class Loader implements Loader_Interface {

	/**
	 * Init script and style
	 */
	public function run( array $assets ) {

		foreach ( $assets as $type => $items ) {

			/**
			 * With this hook you can filter the enqueue script and style config
			 * Filters name:
			 * 'italystrap_config_enqueue_style'
			 * 'italystrap_config_enqueue_script'
			 *
			 * @var array
			 */
			$items = (array) apply_filters(
				'italystrap_config_enqueue_' . strtolower( $type ),
				$items,
				current_filter()
			);

			foreach ( $items as $item ) {
				Asset_Factory::make( $item, $type )->register();
			}
		}
	}
}
