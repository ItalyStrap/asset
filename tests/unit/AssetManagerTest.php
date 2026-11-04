<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetInterface;
use ItalyStrap\Asset\AssetManager;
use UnitTester;

class AssetManagerTest extends Unit
{
    use ProphecyTrait;


    /**
     * @var UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $asset = null;

    public function getAsset(): AssetInterface
    {
        return $this->asset->reveal();
    }

	// phpcs:ignore
	protected function _before() {
        $this->asset = $this->prophesize(AssetInterface::class);
    }

	// phpcs:ignore
    protected function _after() {
    }

    private function getInstance(): AssetManager
    {
        $sut = new AssetManager();
        $this->assertInstanceOf(AssetManager::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function instanceOk(): void
    {
        $this->getInstance();
    }

    /**
     * @test
     */
    public function itShouldAddAssets(): void
    {
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
    public function itShouldThrownRunTimeExceptionIfNoAssetsAreProvided(): void
    {
        $sut = $this->getInstance();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No assets are provided');
        $sut->setup();
    }

    /**
     * @test
     */
    public function itShouldEnqueueAssets(): void
    {
        $this->asset->shouldEnqueue()->willReturn(true);
        $this->asset->enqueue()->will(function (): void {
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
    public function itShouldOnlyRegisterAssets(): void
    {
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
