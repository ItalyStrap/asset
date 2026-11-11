<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Where the scripts are registered, the core in Core\CoreScriptRegistry.
 *
 * Needs WordPress 6.3 or later, for the loading strategy of the scripts.
 */
interface ScriptRegistry
{
    /**
     * @param non-empty-string $handle
     * @param non-empty-string|false $source
     * @param list<non-empty-string> $dependencies
     * @param array{in_footer: bool, strategy?: 'defer'|'async'} $loading
     */
    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        array $loading,
    ): void;

    /**
     * @param non-empty-string $handle
     * @param 'before'|'after' $position
     */
    public function addInline(string $handle, string $code, string $position): void;

    /**
     * @param non-empty-string $handle
     * @param non-empty-string $key
     */
    public function addData(string $handle, string $key, string|bool $value): void;

    /**
     * @param non-empty-string $handle
     * @param string $path Empty for the languages folder of the core.
     */
    public function translate(string $handle, string $domain, string $path): void;

    /**
     * @param non-empty-string $handle
     */
    public function enqueue(string $handle): void;

    /**
     * @param non-empty-string $handle
     */
    public function is(string $handle, AssetStatus $status): bool;

    /**
     * Prints the handle and its dependencies now.
     *
     * @param non-empty-string $handle
     */
    public function print(string $handle): void;

    /**
     * @param non-empty-string $handle
     */
    public function dequeue(string $handle): void;

    /**
     * @param non-empty-string $handle
     */
    public function deregister(string $handle): void;

    /**
     * @param non-empty-string $handle
     * @param non-empty-string $objectName The global object of the data, like myPlugin.
     * @param array<string, mixed> $data
     */
    public function localize(string $handle, string $objectName, array $data): void;
}
