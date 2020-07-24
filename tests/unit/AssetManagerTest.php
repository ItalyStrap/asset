<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\AssetManager;
use UnitTester;

class AssetManagerTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $asset;

	/**
	 * @return AssetInterface
	 */
	public function getAsset(): AssetInterface {
		return $this->asset->reveal();
	}

	// phpcs:ignore
	protected function _before() {
		$this->asset = $this->prophesize(AssetInterface::class);
	}

	// phpcs:ignore
    protected function _after() {
	}

	/**
	 * @return AssetManager
	 */
	private function getInstance(): AssetManager {
		$sut = new AssetManager();
		$this->assertInstanceOf( AssetManager::class, $sut, '');
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
	public function itShouldAddAssets() {
		$sut = $this->getInstance();
		$sut->withAssets(
			$this->getAsset(),
			$this->getAsset(),
			$this->getAsset()
		);
	}

	/**
	 * @test
	 */
	public function itShouldThrownRunTimeExceptionIfNoAssetsAreProvided() {
		$sut = $this->getInstance();
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('No assets are provided');
		$sut->setup();
	}

	/**
	 * @test
	 */
	public function itShouldEnqueueAssets() {
		$this->asset->shouldEnqueue()->willReturn(true);
		$this->asset->enqueue()->will(function () {
		});
		$this->asset->register()->shouldNotBeCalled();

		$sut = $this->getInstance();
		$sut->withAssets(
			$this->getAsset(),
			$this->getAsset(),
			$this->getAsset()
		);

		$sut->setup();
	}

	/**
	 * @test
	 */
	public function itShouldOnlyRegisterAssets() {
		$this->asset->shouldEnqueue()->willReturn(false);
		$this->asset->enqueue()->shouldNotBeCalled();
		$this->asset->register()->shouldBeCalled();

		$sut = $this->getInstance();
		$sut->withAssets(
			$this->getAsset(),
			$this->getAsset(),
			$this->getAsset()
		);

		$sut->setup();
	}
}
