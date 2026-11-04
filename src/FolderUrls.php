<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use SplFileInfo;

/**
 * The URL of a file from the first folder that has it.
 */
final readonly class FolderUrls implements FileUrl
{
    /**
     * @param array<string, string> $folders The paths of the folders and their URLs, without the trailing slash.
     */
    public function __construct(
        private array $folders,
    ) {
    }

    public function url(SplFileInfo $file): string
    {
        foreach ($this->folders as $folder => $url) {
            $relative = (new RelativePath(new SplFileInfo($folder)))->of($file);
            if ($relative !== null) {
                return $url . '/' . $relative;
            }
        }

        throw new InvalidArgumentException(\sprintf(
            'The file "%s" is not in the folders %s',
            $file->getPathname(),
            \implode(', ', \array_keys($this->folders))
        ));
    }
}
