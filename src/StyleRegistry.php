<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Where the styles are registered, the core in Core\CoreStyleRegistry.
 */
interface StyleRegistry
{
    /**
     * @param non-empty-string $handle
     * @param non-empty-string|false $source
     * @param list<non-empty-string> $dependencies
     */
    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        string $media,
    ): void;

    /**
     * @param non-empty-string $handle
     */
    public function addInline(string $handle, string $css): void;

    /**
     * @param non-empty-string $handle
     * @param 'alt'|'path'|'rtl'|'suffix'|'title' $key
     */
    public function addData(string $handle, string $key, string|bool $value): void;

    /**
     * @param non-empty-string $handle
     */
    public function enqueue(string $handle): void;
}
