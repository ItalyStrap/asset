<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Event\ParameterKeys;
use ItalyStrap\Event\SubscriberInterface;

class AssetsSubscriber implements SubscriberInterface {

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
	public function getSubscribedEvents(): array {
		return [
			'wp_enqueue_scripts'	=> [
				ParameterKeys::CALLBACK => [ $this->manager, 'register' ]
			],
		];
	}
}
