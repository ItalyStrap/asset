<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Event\SubscriberInterface;

final class AssetsSubscriber implements SubscriberInterface {

	/**
	 * @var AssetManager
	 */
	private $manager;

	/**
	 * AssetsSubscriber constructor.
	 * @param AssetManager $manager
	 */
	public function __construct( AssetManager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): iterable {
		yield AssetManager::EVENT_NAME	=> [
			static::CALLBACK => 'execute'
		];
	}

	/**
	 *
	 */
	public function execute(): void {
		$this->manager->setup();
	}
}
