<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Debug;

use ItalyStrap\Asset\Style;
use ItalyStrap\Config\ConfigInterface;

final class DebugStyle extends DebugAsset
{
    /**
     * @throws \ReflectionException
     */
    protected function getAssetInstance(ConfigInterface $config): void
    {
        $this->asset = new Style($config);
    }
}
