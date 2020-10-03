<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

final class AssetManager {

	public const EVENT_NAME = 'wp_enqueue_scripts';

	/**
	 * @var array Asset
	 */
	private $assets = [];

	/**
	 * @param AssetInterface ...$assets
	 */
	public function withAssets( AssetInterface ...$assets ): void {
		$this->assets = \array_merge($assets, $this->assets);
	}

	public function setup(): void {
		if ( empty( $this->assets ) ) {
			throw new \RuntimeException('No assets are provided');
		}

		\array_walk($this->assets, function ( AssetInterface $asset ) {
			if ( $asset->shouldEnqueue() ) {
				$asset->enqueue();
			} else {
				$asset->register();
			}
		});
	}
}
