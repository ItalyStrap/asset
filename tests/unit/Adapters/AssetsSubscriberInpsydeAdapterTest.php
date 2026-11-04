<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Codeception\Test\Unit;
use DG\BypassFinals;
use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Asset\Adapters\InpsydeAssetsSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use UnitTester;

class AssetsSubscriberInpsydeAdapterTest extends Unit
{
    use ProphecyTrait;


    /**
     * @var UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $asset_manager = null;

    private ?ObjectProphecy $asset_loader = null;

    private ?ObjectProphecy $asset_asset = null;

    public function getAssetAsset(): Asset
    {
        return $this->asset_asset->reveal();
    }

    public function getAssetLoader(): LoaderInterface
    {
        return $this->asset_loader->reveal();
    }

    public function getAssetManager(): AssetManager
    {
        return $this->asset_manager->reveal();
    }

    protected function _before()
    {
        $this->asset_loader = $this->prophesize(LoaderInterface::class);
        $this->asset_asset = $this->prophesize(Asset::class);

        /**
         * https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/
         */
        BypassFinals::allowPaths(['*/vendor/inpsyde/*']);
        BypassFinals::enable(bypassReadOnly: false);
        $this->asset_manager = $this->prophesize(AssetManager::class);
    }

    protected function _after()
    {
    }

    private function getAssetsSubscriberInpsydeAdapter(): InpsydeAssetsSubscriber
    {
        $sut = new InpsydeAssetsSubscriber($this->getAssetLoader(), []);
        $this->assertInstanceOf(InpsydeAssetsSubscriber::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function instanceOk(): void
    {
        $this->getAssetsSubscriberInpsydeAdapter();
    }

    /**
     * @test
     */
    public function itShouldHaveEventFromGetSubscribedEvents(): void
    {
        $sut = $this->getAssetsSubscriberInpsydeAdapter();
        $array = $sut->getSubscribedEvents();

        $this->assertArrayHasKey(AssetManager::ACTION_SETUP, $array, '');
        foreach ($array as $item) {
            $this->assertTrue(
                \in_array(InpsydeAssetsSubscriber::CALLBACK_METHOD_NAME, $item, true),
                ''
            );
        }
    }

    /**
     * @test
     */
    public function itShouldHaveEventFromGetSubscribedEventsfd(): void
    {
        $return_value = [
            $this->getAssetAsset(),
            $this->getAssetAsset(),
        ];

        $this->asset_loader
            ->load(Argument::any())
            ->willReturn($return_value)
            ->shouldBeCalled(1);

        $obj = $this;
        $this->asset_manager->register(...$return_value)->will(
            fn(): AssetManager => $obj->getAssetManager()
        )->shouldBeCalled(1);

        $sut = $this->getAssetsSubscriberInpsydeAdapter();
        $sut->loadAssets($this->getAssetManager());
    }
}
