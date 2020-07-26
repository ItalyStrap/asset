<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use DG\BypassFinals;
use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Asset\Adapters\InpsydeAssetsSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use UnitTester;

class AssetsSubscriberInpsydeAdapterTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	private $asset_manager;

	/**
	 * @var ObjectProphecy
	 */
	private $asset_loader;

	/**
	 * @var ObjectProphecy
	 */
	private $asset_asset;

	/**
	 * @return Asset
	 */
	public function getAssetAsset(): Asset {
		return $this->asset_asset->reveal();
	}

	/**
	 * @return LoaderInterface
	 */
	public function getAssetLoader(): LoaderInterface {
		return $this->asset_loader->reveal();
	}

	/**
	 * @return AssetManager
	 */
	public function getAssetManager(): AssetManager {
		return $this->asset_manager->reveal();
	}

	protected function _before() {
		$this->asset_loader = $this->prophesize( LoaderInterface::class );
		$this->asset_asset = $this->prophesize( Asset::class );

		/**
		 * https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/
		 */
		BypassFinals::enable();
		$this->asset_manager = $this->prophesize( AssetManager::class );
	}

	protected function _after() {
	}

	/**
	 * @return InpsydeAssetsSubscriber
	 */
	private function getAssetsSubscriberInpsydeAdapter(): InpsydeAssetsSubscriber {
		$sut = new InpsydeAssetsSubscriber( $this->getAssetLoader(), [] );
		$this->assertInstanceOf( InpsydeAssetsSubscriber::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getAssetsSubscriberInpsydeAdapter();
	}

	/**
	 * @test
	 */
	public function itShouldHaveEventFromGetSubscribedEvents() {
		$sut = $this->getAssetsSubscriberInpsydeAdapter();
		$array = $sut->getSubscribedEvents();

		$this->assertArrayHasKey( AssetManager::ACTION_SETUP, $array, '');
		foreach ( $array as $item ) {
			$this->assertTrue(
				\in_array( InpsydeAssetsSubscriber::CALLBACK_METHOD_NAME, $item, true ),
				''
			);
		}
	}

	/**
	 * @test
	 */
	public function itShouldHaveEventFromGetSubscribedEventsfd() {
		$this->asset_loader
			->load(Argument::any())
			->willReturn(
				[
					$this->getAssetAsset()
				]
			)
			->shouldBeCalled(1);

		$this->asset_manager->register( Argument::any() )->shouldBeCalled(1);

		$sut = $this->getAssetsSubscriberInpsydeAdapter();
		$sut->loadAssets( $this->getAssetManager() );
	}
}
