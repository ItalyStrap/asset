<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Loader;


use ItalyStrap\Asset\AssetFactory;

class GeneratorLoader
{
	public function load( \Generator $configs ) {

		$assets = [];
		foreach ( $configs as $config ) {
			$assets[] = ( new AssetFactory() )->make($config);
		}

		return $assets;
	}
}