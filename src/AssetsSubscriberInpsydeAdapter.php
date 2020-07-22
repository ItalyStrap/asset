<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use Inpsyde\Assets\AssetManager;
use ItalyStrap\Event\ParameterKeys;
use ItalyStrap\Event\SubscriberInterface;

class AssetsSubscriberInpsydeAdapter implements SubscriberInterface {

	const CALLBACK_METHOD_NAME = 'loadAssets';

	/**
	 * @var \Inpsyde\Assets\Loader\LoaderInterface
	 */
	private $loader;
	private $resource;

	/**
	 * AssetsSubscriberInpsydeAdapter constructor.
	 * @param \Inpsyde\Assets\Loader\LoaderInterface $loader
	 * @param $resource
	 */
	public function __construct( \Inpsyde\Assets\Loader\LoaderInterface $loader, $resource ) {
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
