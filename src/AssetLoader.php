<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Loads the scripts and the styles, call it on the event of the assets, see EventName.
 */
final readonly class AssetLoader
{
    public function __construct(
        private ScriptLoader $scripts = new ScriptLoader(),
        private StyleLoader $styles = new StyleLoader(),
    ) {
    }

    public function load(Script|Style ...$assets): void
    {
        foreach ($assets as $asset) {
            $this->loadOne($asset);
        }
    }

    private function loadOne(Script|Style $asset): void
    {
        if ($asset instanceof Script) {
            $this->scripts->load($asset);
            return;
        }

        $this->styles->load($asset);
    }
}
