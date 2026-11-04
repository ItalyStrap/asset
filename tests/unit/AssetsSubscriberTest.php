<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Asset\EventName;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoader;
use ItalyStrap\Asset\StyleLoader;
use ItalyStrap\Asset\Tests\Unit\Fake\ScriptRegistrySpy;
use ItalyStrap\Asset\Tests\Unit\Fake\StyleRegistrySpy;
use ItalyStrap\Event\SubscriberInterface;
use PHPUnit\Framework\Attributes\Test;

final class AssetsSubscriberTest extends Unit
{
    #[Test]
    public function itLoadsOnTheFrontEndByDefault(): void
    {
        $events = \iterator_to_array((new AssetsSubscriber(new AssetLoader(), []))->getSubscribedEvents());

        $this->assertSame(
            [EventName::FRONT_END => [SubscriberInterface::CALLBACK => 'load', SubscriberInterface::PRIORITY => 10]],
            $events
        );
    }

    #[Test]
    public function itLoadsOnTheEventWithThePriority(): void
    {
        $priority = \PHP_INT_MAX - 50;
        $subscriber = new AssetsSubscriber(new AssetLoader(), [], EventName::LOGIN, $priority);

        $this->assertSame(
            [EventName::LOGIN => [SubscriberInterface::CALLBACK => 'load', SubscriberInterface::PRIORITY => $priority]],
            \iterator_to_array($subscriber->getSubscribedEvents())
        );
    }

    #[Test]
    public function itLoadsTheAssetsWhenTheEventRuns(): void
    {
        $scripts = new ScriptRegistrySpy();
        $loader = new AssetLoader(new ScriptLoader($scripts), new StyleLoader(new StyleRegistrySpy()));

        (new AssetsSubscriber($loader, [new Script(new Handle('my-script'), 'https://example.com/my-script.js')]))
            ->load();

        $this->assertSame(['register', 'enqueue'], \array_column($scripts->calls, 0));
    }
}
