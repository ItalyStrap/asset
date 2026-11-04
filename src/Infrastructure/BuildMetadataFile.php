<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Infrastructure;

use ItalyStrap\Asset\Domain\BuildMetadata;
use ItalyStrap\Asset\Domain\Dependencies;
use ItalyStrap\Asset\Domain\FixedVersion;
use RuntimeException;
use SplFileInfo;

/**
 * The .asset.php file next to a built file, build/index.js and build/index.css read build/index.asset.php.
 */
final readonly class BuildMetadataFile
{
    private string $path;

    /**
     * @param SplFileInfo $builtFile The built script or style, a file of the Finder too.
     */
    public function __construct(SplFileInfo $builtFile)
    {
        $this->path = \sprintf(
            '%s/%s.asset.php',
            $builtFile->getPath(),
            $builtFile->getBasename('.' . $builtFile->getExtension())
        );
    }

    /**
     * False before the build, the file is not in the repository.
     */
    public function exists(): bool
    {
        return \is_readable($this->path);
    }

    /**
     * @throws RuntimeException When the file is missing or it has no version.
     */
    public function read(): BuildMetadata
    {
        if (!$this->exists()) {
            throw new RuntimeException(\sprintf('The build metadata "%s" is not readable', $this->path));
        }

        /** @psalm-suppress UnresolvableInclude */
        $metadata = require $this->path;
        if (!\is_array($metadata) || !\is_string($metadata['version'] ?? null)) {
            throw new RuntimeException(\sprintf('The build metadata "%s" has no version', $this->path));
        }

        return new BuildMetadata(
            new Dependencies(...$this->dependencies($metadata)),
            new FixedVersion($metadata['version'])
        );
    }

    /**
     * @param array<mixed> $metadata
     * @return list<string>
     */
    private function dependencies(array $metadata): array
    {
        $dependencies = $metadata['dependencies'] ?? [];
        if (!\is_array($dependencies)) {
            return [];
        }

        return \array_values(\array_filter($dependencies, \is_string(...)));
    }
}
