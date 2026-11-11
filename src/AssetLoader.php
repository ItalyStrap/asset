<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Loads the scripts, the styles and the script modules, call it on the event of the assets, see EventName.
 *
 * Needs the WordPress of its loaders: 6.3 for the scripts, 3.6 for the styles, 6.5 for the script modules.
 */
final readonly class AssetLoader
{
    public function __construct(
        private ScriptLoader $scripts = new ScriptLoader(),
        private StyleLoader $styles = new StyleLoader(),
        private ScriptModuleLoader $modules = new ScriptModuleLoader(),
    ) {
    }

    public function load(Script|Style|ScriptModule ...$assets): void
    {
        foreach ($assets as $asset) {
            $this->loadOne($asset);
        }
    }

    private function loadOne(Script|Style|ScriptModule $asset): void
    {
        if ($asset instanceof Script) {
            $this->scripts->load($asset);
            return;
        }

        if ($asset instanceof Style) {
            $this->styles->load($asset);
            return;
        }

        $this->modules->load($asset);
    }
}
