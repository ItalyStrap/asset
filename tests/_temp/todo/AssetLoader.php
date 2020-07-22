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
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Asset_Loader
 */
class AssetLoader implements LoaderInterface {

	const FILTER_PREFIX = 'italystrap_config_enqueue_';

	/**
	 * Init script and style
	 */
	public function load( array $assets ) {

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
				self::FILTER_PREFIX . strtolower( $type ),
				$items,
				current_filter()
			);

			foreach ( $items as $item ) {
				AssetFactory::make( $type, $item )->boot();
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function boot() {

		$config = \array_merge( $this->getDefaultStructure(), $this->config->all() );

		if ( isset( $config['pre_register'] ) ) {
			$this->register();
			return true; // <- This will continue and it wont load the localized object.
		}

		if ( $this->isLoadOn( $config ) ) {
			$this->enqueue( $config );
		}

		if ( empty( $config['localize'] ) ) {
			return true;
		}

		if ( is_array( $config['localize'] ) ) {
			$this->localize_script( $config );
		}

		return true;
	}

	/**
	 * Get the default structure.
	 *
	 * @return array
	 */
	protected function getDefaultStructure() {

		/**
		 * Style
		 */
//		return [
//			'handle'	=> '',
//			'file'		=> null,
//			'deps'		=> null,
//			'version'	=> null,
//			'media'		=> null,
//		];

		/**
		 * Script
		 */
//		return [
//			'handle'	=> '',
//			'file'		=> null,
//			'deps'		=> null,
//			'version'	=> null,
//			'in_footer'	=> true,
//			'localize'  => '',
//			'position'  => 'after',
//		];
	}

	/**
	 * Loading asset conditionally.
	 * @todo Refactor this, see in builder class on ItalyStrap theme
	 *
	 * @param $config
	 * @return bool
	 */
	private function isLoadOn( $config ) {
		/**
		 * Default. Return true
		 *
		 * @var bool
		 */
		$bool = true;

		if ( ! isset( $config['load_on'] ) ) {
			return $bool;
		}

		if ( is_callable( $config['load_on'] ) ) {
			return (bool) call_user_func( $config['load_on'] );
		}

		/**
		 * Example:
		 * 'load_on'		=> false,
		 * 'load_on'		=> true,
		 * 'load_on'		=> is_my_function\return_bool(),
		 */
		return (bool) $config['load_on'];
	}
}
