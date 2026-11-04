<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;
use SplFileInfo;

/**
 * The path of a file from a folder, both resolved without the symlinks.
 */
final readonly class RelativePath
{
    public function __construct(
        private SplFileInfo $folder,
    ) {
    }

    /**
     * Null when the file is not in the folder.
     */
    public function of(SplFileInfo $file): ?string
    {
        $folder = $this->folder->getRealPath();
        $path = $file->getRealPath();
        if ($folder === false || $path === false) {
            return null;
        }

        $prefix = $folder . \DIRECTORY_SEPARATOR;
        if (!\str_starts_with($path, $prefix)) {
            return null;
        }

        return \str_replace(\DIRECTORY_SEPARATOR, '/', \substr($path, \strlen($prefix)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function orFail(SplFileInfo $file): string
    {
        return $this->of($file) ?? throw new InvalidArgumentException(\sprintf(
            'The file "%s" is not in "%s"',
            $file->getPathname(),
            $this->folder->getPathname()
        ));
    }
}
