<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Fake;

use ItalyStrap\Asset\AssetStatus;
use ItalyStrap\Asset\StyleRegistry;

/**
 * Records the calls in order, the tests read them like the core would receive them.
 */
final class StyleRegistrySpy implements StyleRegistry
{
    /** @var list<array<int, mixed>> */
    public array $calls = [];

    /** @var array<string, list<AssetStatus>> The statuses of each handle, read by is(). */
    public array $statuses = [];

    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        string $media,
    ): void {
        $this->calls[] = ['register', $handle, $source, $dependencies, $version, $media];
    }

    public function addInline(string $handle, string $css): void
    {
        $this->calls[] = ['addInline', $handle, $css];
    }

    public function addData(string $handle, string $key, string|bool $value): void
    {
        $this->calls[] = ['addData', $handle, $key, $value];
    }

    public function enqueue(string $handle): void
    {
        $this->calls[] = ['enqueue', $handle];
    }

    public function is(string $handle, AssetStatus $status): bool
    {
        $this->calls[] = ['is', $handle, $status->value];

        return \in_array($status, $this->statuses[$handle] ?? [], true);
    }

    public function print(string $handle): void
    {
        $this->calls[] = ['print', $handle];
    }

    public function dequeue(string $handle): void
    {
        $this->calls[] = ['dequeue', $handle];
    }

    public function deregister(string $handle): void
    {
        $this->calls[] = ['deregister', $handle];
    }
}
