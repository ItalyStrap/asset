<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Adapters;

use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetFactory;
use Inpsyde\Assets\BaseAsset;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Config\ConfigInterface;
use function array_map;

final class InpsydeGeneratorLoader implements LoaderInterface {

	/**
	 * @inheritDoc
	 */
	public function load( $data ): array {

		$assets = [];

		/** @var ConfigInterface $config */
		foreach ( $data as $config ) {
			$assets[] = AssetFactory::create($config->toArray());
		}

		/**
		 * @psalm-suppress ArgumentTypeCoercion
		 */
		$assets = array_map(
			static function (BaseAsset $asset): BaseAsset {
				return $asset->disableAutodiscoverVersion();
			},
			$assets
		);

		return $assets;
	}
}
