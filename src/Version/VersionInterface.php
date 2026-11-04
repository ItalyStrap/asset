<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Version;

interface VersionInterface
{
    /**
     * @return mixed|string|bool|null
     */
    public function version(\SplFileInfo $fileInfo, array $config);
}
