<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\Event\EventDispatcher;
use ItalyStrap\Event\SubscriberRegister;
use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetManager;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Event\SubscriberInterface;
use UnitTester;

use function tad\FunctionMockerLe\define;

class AssetSubscriberTest extends Unit
{
    use ProphecyTrait;


    /**
     * @var UnitTester
     */
    protected $tester;
    private ?ObjectProphecy $asset_manager = null;

    public function getAssetManager(): AssetManager
    {
        return $this->asset_manager->reveal();
    }

    protected function _before()
    {
        $this->asset_manager = $this->prophesize(AssetManager::class);
    }

    protected function _after()
    {
    }

    private function getInstance(): AssetsSubscriber
    {
        $sut = new AssetsSubscriber($this->getAssetManager());
        $this->assertInstanceOf(SubscriberInterface::class, $sut, '');
        $this->assertInstanceOf(AssetsSubscriber::class, $sut, '');
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
    public function itShouldHaveEventsSubscribed(): void
    {
        $sut = $this->getInstance();
        $sut->getSubscribedEvents();
    }

    /**
     * @test
     */
    public function itShouldExecute(): void
    {
        $sut = $this->getInstance();
        $sut->execute();
    }

    /**
     * @test
     */
    public function itShouldNotThrownAnyErrorsWhenSutIsSubscribedOnEvent(): void
    {
        define('add_filter', fn(): true => true);

        $event_dispatcher = new EventDispatcher();
        $event_subscriber = new SubscriberRegister($event_dispatcher);
        $sut = $this->getInstance();

        $event_subscriber->addSubscriber($sut);
    }
}
