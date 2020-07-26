<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Config\ConfigInterface;

final class AssetFactory
{
	public function make( ConfigInterface $config ) {
		$type = $config->get( 'type' );

		$instance = new $type( $config );

		if ( ! $instance instanceof AssetInterface ) {
			throw new \InvalidArgumentException(\sprintf(
				'The class %s must implements %s',
				$type,
				AssetInterface::class
			));
		}

		return new $type( $config );
	}
}