<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Core;

use ItalyStrap\Asset\AssetStatus;
use ItalyStrap\Asset\ScriptRegistry;

/**
 * The scripts of the core, one function for each call, the integration suite tests it.
 *
 * Needs WordPress 6.3 or later, for the loading strategy of the scripts.
 */
final readonly class CoreScriptRegistry implements ScriptRegistry
{
    public function register(
        string $handle,
        string|false $source,
        array $dependencies,
        string|false|null $version,
        array $loading,
    ): void {
        \wp_register_script($handle, $source, $dependencies, $version, $loading);
    }

    public function addInline(string $handle, string $code, string $position): void
    {
        \wp_add_inline_script($handle, $code, $position);
    }

    public function addData(string $handle, string $key, string|bool $value): void
    {
        \wp_script_add_data($handle, $key, $value);
    }

    public function translate(string $handle, string $domain, string $path): void
    {
        \wp_set_script_translations($handle, $domain, $path);
    }

    public function enqueue(string $handle): void
    {
        \wp_enqueue_script($handle);
    }

    public function is(string $handle, AssetStatus $status): bool
    {
        return \wp_script_is($handle, $status->value);
    }

    public function print(string $handle): void
    {
        \wp_print_scripts([$handle]);
    }

    public function dequeue(string $handle): void
    {
        \wp_dequeue_script($handle);
    }

    public function deregister(string $handle): void
    {
        \wp_deregister_script($handle);
    }

    public function localize(string $handle, string $objectName, array $data): void
    {
        \wp_localize_script($handle, $objectName, $data);
    }
}
