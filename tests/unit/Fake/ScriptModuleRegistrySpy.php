<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Fake;

use ItalyStrap\Asset\ScriptModuleRegistry;

/**
 * Records the calls in order, the tests read them like the core would receive them.
 */
final class ScriptModuleRegistrySpy implements ScriptModuleRegistry
{
    /** @var list<array<int, mixed>> */
    public array $calls = [];

    public function register(string $id, string $source, array $dependencies, string|false|null $version): void
    {
        $this->calls[] = ['register', $id, $source, $dependencies, $version];
    }

    public function translate(string $id, string $domain, string $path): void
    {
        $this->calls[] = ['translate', $id, $domain, $path];
    }

    public function enqueue(string $id): void
    {
        $this->calls[] = ['enqueue', $id];
    }

    public function dequeue(string $id): void
    {
        $this->calls[] = ['dequeue', $id];
    }

    public function deregister(string $id): void
    {
        $this->calls[] = ['deregister', $id];
    }
}
