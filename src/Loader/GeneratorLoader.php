<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Loader;

use ItalyStrap\Asset\AssetFactory;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Config\ConfigInterface;

final class GeneratorLoader {

	/**
	 * @param iterable $configs
	 * @return array<AssetInterface>
	 */
	public function load( iterable $configs ): iterable {

		$assets = [];
		/** @var ConfigInterface $config */
		foreach ( $configs as $config ) {
			$assets[] = ( new AssetFactory() )->make($config);
//			yield ( new AssetFactory() )->make($config);
		}

		return $assets;
	}
}
