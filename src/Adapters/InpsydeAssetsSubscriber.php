<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Adapters;

use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Event\ParameterKeys;
use ItalyStrap\Event\SubscriberInterface;

class InpsydeAssetsSubscriber implements SubscriberInterface {

	const CALLBACK_METHOD_NAME = 'loadAssets';

	/**
	 * @var LoaderInterface
	 */
	private $loader;
	private $resource;

	/**
	 * AssetsSubscriberInpsydeAdapter constructor.
	 * @param LoaderInterface $loader
	 * @param $resource
	 */
	public function __construct( LoaderInterface $loader, $resource ) {
		$this->loader = $loader;
		$this->resource = $resource;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array {
		return [
			AssetManager::ACTION_SETUP	=> [
				ParameterKeys::CALLBACK	=> 'loadAssets',
			],
		];
	}

	/**
	 * @param AssetManager $manager
	 */
	public function loadAssets( AssetManager $manager ) {
		$assets = $this->loader->load($this->resource);

		foreach ($assets as $asset ) {
			$manager->register( $asset );
		}
	}
}
