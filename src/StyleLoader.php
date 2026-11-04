<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Core\CoreStyleRegistry;

/**
 * Registers a style with its inline styles, then enqueues it when its condition is satisfied.
 */
final readonly class StyleLoader
{
    public function __construct(
        private StyleRegistry $registry = new CoreStyleRegistry(),
    ) {
    }

    public function load(Style $style): void
    {
        $handle = $style->handle()->value();

        $this->registry->register(
            $handle,
            $style->source() ?? false,
            $style->dependencies()->toArray(),
            $style->version()->value(),
            $style->media()
        );

        foreach ($style->inlineStyles() as $inlineStyle) {
            $this->registry->addInline($handle, $inlineStyle);
        }

        foreach ($style->data() as $key => $value) {
            $this->registry->addData($handle, $key, $value);
        }

        if (!$style->condition()->isSatisfied()) {
            return;
        }

        $this->registry->enqueue($handle);
    }
}
