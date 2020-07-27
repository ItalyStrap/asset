<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Debug;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\Style;
use ItalyStrap\Config\ConfigInterface;

abstract class DebugAsset implements \ItalyStrap\Asset\AssetInterface {

	/**
	 * @var AssetInterface
	 */
	protected $asset;


	/**
	 * DebugStyle constructor.
	 * @param ConfigInterface $config
	 */
	public function __construct( ConfigInterface $config ) {

		$url = $config->get( Asset::URL );

		$response = \wp_remote_get( $url );
		if ( \is_wp_error( $response ) ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'The url "%s" is not accessible',
					\strval( $url )
				)
			);
		}

		$this->getAssetInstance( $config );
	}

	/**
	 * @inheritDoc
	 */
	public function location(): string {
		return $this->asset->location();
	}

	/**
	 * @inheritDoc
	 */
	public function isEnqueued(): bool {
		return $this->asset->isEnqueued();
	}

	/**
	 * @inheritDoc
	 */
	public function isRegistered(): bool {
		return $this->asset->isRegistered();
	}

	/**
	 * @inheritDoc
	 */
	public function shouldEnqueue(): bool {
		return $this->asset->shouldEnqueue();
	}

	/**
	 * @inheritDoc
	 */
	public function register(): bool {
		return $this->asset->register();
	}

	/**
	 * @inheritDoc
	 */
	public function enqueue(): void {
		$this->asset->enqueue();
	}

	/**
	 * @param ConfigInterface $config
	 * @throws \ReflectionException
	 */
	abstract protected function getAssetInstance( ConfigInterface $config ): void;
}
