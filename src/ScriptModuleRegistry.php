<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Where the script modules are registered, the core in Core\CoreScriptModuleRegistry.
 */
interface ScriptModuleRegistry
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $source
     * @param list<non-empty-string> $dependencies
     */
    public function register(string $id, string $source, array $dependencies, string|false|null $version): void;

    /**
     * Needs WordPress 7.0.
     *
     * @param non-empty-string $id
     * @param string $path Empty for the languages folder of the core.
     */
    public function translate(string $id, string $domain, string $path): void;

    /**
     * @param non-empty-string $id
     */
    public function enqueue(string $id): void;

    /**
     * @param non-empty-string $id
     */
    public function dequeue(string $id): void;

    /**
     * @param non-empty-string $id
     */
    public function deregister(string $id): void;
}
