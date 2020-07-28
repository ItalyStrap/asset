<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use ItalyStrap\Config\ConfigInterface;
use function sprintf;

final class AssetFactory {

	public function make( ConfigInterface $config ): AssetInterface {
		/** @var string $type */
		$type = $config->get( Asset::TYPE );

		/** @var AssetInterface $instance */
		$instance = new $type( $config );

		if ( ! $instance instanceof AssetInterface ) {
			throw new InvalidArgumentException( sprintf(
				'The class %s must implements %s',
				$type,
				AssetInterface::class
			));
		}

		return $instance;
	}
}
