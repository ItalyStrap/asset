<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Core;

use ItalyStrap\Asset\AssetStatus;
use ItalyStrap\Asset\StyleRegistry;

/**
 * The styles of the core, one function for each call, the integration suite tests it.
 *
 * Needs WordPress 3.6 or later.
 */
final readonly class CoreStyleRegistry implements StyleRegistry
{
    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        string $media,
    ): void {
        \wp_register_style($handle, $source, $dependencies, $version, $media);
    }

    public function addInline(string $handle, string $css): void
    {
        \wp_add_inline_style($handle, $css);
    }

    public function addData(string $handle, string $key, string|bool $value): void
    {
        \wp_style_add_data($handle, $key, $value);
    }

    public function enqueue(string $handle): void
    {
        \wp_enqueue_style($handle);
    }

    public function is(string $handle, AssetStatus $status): bool
    {
        return \wp_style_is($handle, $status->value);
    }

    public function print(string $handle): void
    {
        \wp_print_styles([$handle]);
    }

    public function dequeue(string $handle): void
    {
        \wp_dequeue_style($handle);
    }

    public function deregister(string $handle): void
    {
        \wp_deregister_style($handle);
    }
}
