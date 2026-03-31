<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use ItalyStrap\Config\ConfigInterface;
use function class_exists;
use function is_a;
use function is_string;
use function sprintf;

final class AssetFactory {

	public function make( ConfigInterface $config ): AssetInterface {
		$type = $config->get( Asset::TYPE );

		if ( ! is_string( $type ) || '' === $type ) {
			throw new InvalidArgumentException( 'The asset type must be a non-empty class name string' );
		}

		if ( ! class_exists( $type ) ) {
			throw new InvalidArgumentException( sprintf(
				'The class %s does not exist',
				$type
			) );
		}

		if ( ! is_a( $type, AssetInterface::class, true ) ) {
			throw new InvalidArgumentException( sprintf(
				'The class %s must implement %s',
				$type,
				AssetInterface::class
			));
		}

		return new $type( $config );
	}
}
