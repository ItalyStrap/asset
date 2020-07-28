<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Loader;

use ItalyStrap\Asset\AssetFactory;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Config\ConfigInterface;

class GeneratorLoader {

	/**
	 * @param \Generator $configs
	 * @return array<AssetInterface>
	 */
	public function load( \Generator $configs ): array {

		$assets = [];
		/** @var ConfigInterface $config */
		foreach ( $configs as $config ) {
			$assets[] = ( new AssetFactory() )->make($config);
		}

		return $assets;
	}
}
