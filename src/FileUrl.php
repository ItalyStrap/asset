<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use SplFileInfo;

/**
 * The URL of a file, from where it is on the disk.
 */
interface FileUrl
{
    /**
     * @throws InvalidArgumentException When the file is not in the folder of the URL.
     */
    public function url(SplFileInfo $file): string;
}
