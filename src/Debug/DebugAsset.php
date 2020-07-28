<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Debug;

use InvalidArgumentException;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Config\ConfigInterface;
use ReflectionException;
use function is_wp_error;
use function sprintf;
use function strval;
use function wp_remote_get;

abstract class DebugAsset implements AssetInterface
{
	public const M_URL_NOT_ACCESSIBLE = 'The url "%s" is not accessible';

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

		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			throw new InvalidArgumentException(
				sprintf(
					self::M_URL_NOT_ACCESSIBLE,
					strval( $url )
				)
			);
		}

		$this->getAssetInstance( $config );
	}

	/**
	 * @inheritDoc
	 */
	public function handle(): string {
		return $this->asset->handle();
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
	 * @throws ReflectionException
	 */
	abstract protected function getAssetInstance( ConfigInterface $config ): void;
}
