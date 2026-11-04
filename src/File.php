<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Version\VersionInterface;
use SplFileInfo;

use function rtrim;
use function str_replace;
use function strval;

final readonly class File implements FileInterface
{
    private string $base_url;

    private string $base_path;

    /**
     * File constructor.
     */
    public function __construct(
        private SplFileInfo $file,
        private VersionInterface $version,
        string $base_url,
        string $base_path
    ) {
        $this->base_url = rtrim($base_url, '\/');
        $this->base_path = rtrim($base_path, '\/');
    }

    /**
     * @inheritDoc
     */
    public function version()
    {
        if ($version = $this->version->version($this->file, [])) {
            return $version;
        }

        return strval($this->file->getMTime());
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return $this->generateUrl();
    }

    private function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    private function generateUrl(): string
    {
        return str_replace(
            $this->normalizePath($this->base_path),
            $this->base_url,
            $this->normalizePath(strval($this->file->getRealPath()))
        );
    }
}
