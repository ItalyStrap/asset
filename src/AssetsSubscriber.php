<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Event\SubscriberInterface;

final readonly class AssetsSubscriber implements SubscriberInterface
{
    /**
     * AssetsSubscriber constructor.
     */
    public function __construct(private AssetManager $manager)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): iterable
    {
        yield AssetManager::EVENT_NAME  => [
            self::CALLBACK => 'execute'
        ];
    }

    /**
     *
     */
    public function execute(): void
    {
        $this->manager->setup();
    }
}
