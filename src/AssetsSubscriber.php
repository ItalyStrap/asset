<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Event\SubscriberInterface;

/**
 * Loads the assets on their event, it needs italystrap/event.
 *
 * Without italystrap/event, call AssetLoader::load() on the event.
 */
final readonly class AssetsSubscriber implements SubscriberInterface
{
    /**
     * @param list<Script|Style|ScriptModule> $assets
     */
    public function __construct(
        private AssetLoader $loader,
        private array $assets,
        private string $hook = EventName::FRONT_END,
        private int $priority = 10,
    ) {
    }

    public function getSubscribedEvents(): iterable
    {
        yield $this->hook => [
            self::CALLBACK => 'load',
            self::PRIORITY => $this->priority,
        ];
    }

    /**
     * The arguments of the event, like the page of the admin, are not used.
     */
    public function load(): void
    {
        $this->loader->load(...$this->assets);
    }
}
