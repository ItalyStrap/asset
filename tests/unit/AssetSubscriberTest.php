<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetManager;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Event\SubscriberInterface;
use UnitTester;

class AssetSubscriberTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $asset_manager;

	/**
	 * @return AssetManager
	 */
	public function getAssetManager(): AssetManager {
		return $this->asset_manager->reveal();
	}

	protected function _before() {
		$this->asset_manager = $this->prophesize( AssetManager::class );
	}

	protected function _after() {
	}

	private function getInstance() {
		$sut = new AssetsSubscriber( $this->getAssetManager() );
		$this->assertInstanceOf( SubscriberInterface::class, $sut, '' );
		$this->assertInstanceOf( AssetsSubscriber::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldRun() {
		$sut = $this->getInstance();
		$subscriberd_events = $sut->getSubscribedEvents();
	}

	/**
	 * @test
	 */
	public function itShouldNotThrownAnyErrors() {
		\tad\FunctionMockerLe\define('add_filter', function (){return true;});

		$event_dispatcher = new \ItalyStrap\Event\EventDispatcher();
		$event_subscriber = new \ItalyStrap\Event\SubscriberRegister( $event_dispatcher );
		$sut = $this->getInstance();

		$event_subscriber->addSubscriber( $sut );

		$sut->getSubscribedEvents();
	}
}
