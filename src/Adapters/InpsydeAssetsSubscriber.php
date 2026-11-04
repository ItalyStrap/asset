<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Adapters;

use Inpsyde\Assets\Asset;
use Inpsyde\Assets\AssetManager;
use Inpsyde\Assets\Loader\LoaderInterface;
use ItalyStrap\Event\SubscriberInterface;

final class InpsydeAssetsSubscriber implements SubscriberInterface
{
    public const CALLBACK_METHOD_NAME = 'loadAssets';

    /**
     * AssetsSubscriberInpsydeAdapter constructor.
     * @param mixed $resource
     */
    public function __construct(private readonly LoaderInterface $loader, private $resource)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            AssetManager::ACTION_SETUP  => [
                self::CALLBACK    => 'loadAssets',
            ],
        ];
    }

    public function loadAssets(AssetManager $manager): void
    {
        /**
         * @var array<Asset>
         */
        $assets = $this->loader->load($this->resource);
        $manager->register(...$assets);
    }
}
