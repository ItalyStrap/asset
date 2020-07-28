<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

class AssetManager {

	public const EVENT_NAME = 'wp_enqueue_scripts';

	/**
	 * @var array Asset
	 */
	private $assets = [];

	/**
	 * @param AssetInterface ...$assets
	 */
	public function withAssets( AssetInterface ...$assets ) {
		$this->assets = \array_merge($assets, $this->assets);
	}

	public function setup() {
		if ( empty( $this->assets ) ) {
			throw new \RuntimeException('No assets are provided');
		}

		\array_walk($this->assets, function ( AssetInterface $asset, $key ) {
			if ( $asset->shouldEnqueue() ) {
				$asset->enqueue();
			} else {
				$asset->register();
			}
		});
	}
}
