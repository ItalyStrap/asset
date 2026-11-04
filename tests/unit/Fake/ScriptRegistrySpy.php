<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Fake;

use ItalyStrap\Asset\ScriptRegistry;

/**
 * Records the calls in order, the tests read them like the core would receive them.
 */
final class ScriptRegistrySpy implements ScriptRegistry
{
    /** @var list<array<int, mixed>> */
    public array $calls = [];

    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        array $loading,
    ): void {
        $this->calls[] = ['register', $handle, $source, $dependencies, $version, $loading];
    }

    public function addInline(string $handle, string $code, string $position): void
    {
        $this->calls[] = ['addInline', $handle, $code, $position];
    }

    public function addData(string $handle, string $key, string|bool $value): void
    {
        $this->calls[] = ['addData', $handle, $key, $value];
    }

    public function translate(string $handle, string $domain, string $path): void
    {
        $this->calls[] = ['translate', $handle, $domain, $path];
    }

    public function enqueue(string $handle): void
    {
        $this->calls[] = ['enqueue', $handle];
    }
}
