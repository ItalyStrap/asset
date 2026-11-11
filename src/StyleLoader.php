<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Core\CoreStyleRegistry;

/**
 * Registers a style with its inline styles, then enqueues it when its condition is satisfied.
 *
 * Needs WordPress 3.6 or later.
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

    public function is(Handle $handle, AssetStatus $status): bool
    {
        return $this->registry->is($handle->value(), $status);
    }

    /**
     * Prints the registered handles now, with their dependencies, like a style registered with RegisterOnly
     * that must be at a given point of the page. A handle not registered is skipped.
     */
    public function print(Handle ...$handles): void
    {
        foreach ($handles as $handle) {
            $this->printOne($handle->value());
        }
    }

    public function dequeue(Handle $handle): void
    {
        $this->registry->dequeue($handle->value());
    }

    public function deregister(Handle $handle): void
    {
        $this->registry->deregister($handle->value());
    }

    /**
     * @param non-empty-string $handle
     */
    private function printOne(string $handle): void
    {
        if (!$this->registry->is($handle, AssetStatus::Registered)) {
            return;
        }

        $this->registry->print($handle);
    }
}
