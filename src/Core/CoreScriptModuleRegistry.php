<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Core;

use ItalyStrap\Asset\ScriptModuleRegistry;

/**
 * The script modules of the core, one function for each call, the integration suite tests it.
 */
final readonly class CoreScriptModuleRegistry implements ScriptModuleRegistry
{
    public function register(string $id, string $source, array $dependencies, string|false|null $version): void
    {
        \wp_register_script_module($id, $source, $dependencies, $version);
    }

    public function translate(string $id, string $domain, string $path): void
    {
        \wp_set_script_module_translations($id, $domain, $path);
    }

    public function enqueue(string $id): void
    {
        \wp_enqueue_script_module($id);
    }

    public function dequeue(string $id): void
    {
        \wp_dequeue_script_module($id);
    }

    public function deregister(string $id): void
    {
        \wp_deregister_script_module($id);
    }
}
