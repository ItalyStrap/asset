<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Core\CoreScriptRegistry;

/**
 * Registers a script with all its parts, then enqueues it when its condition is satisfied.
 */
final readonly class ScriptLoader
{
    public function __construct(
        private ScriptRegistry $registry = new CoreScriptRegistry(),
    ) {
    }

    public function load(Script $script): void
    {
        $handle = $script->handle()->value();

        $this->registry->register(
            $handle,
            $script->source() ?? false,
            $script->dependencies()->toArray(),
            $script->version()->value(),
            $script->loading()->toArray()
        );

        foreach ($script->inlineScripts() as $inlineScript) {
            $this->registry->addInline($handle, $inlineScript->code(), $inlineScript->position()->value);
        }

        foreach ($script->data() as $key => $value) {
            $this->registry->addData($handle, $key, $value);
        }

        $this->translate($handle, $script->translations());
        $this->enqueue($handle, $script->condition());
    }

    /**
     * @param non-empty-string $handle
     */
    private function translate(string $handle, ?Translations $translations): void
    {
        if (!$translations instanceof Translations) {
            return;
        }

        $this->registry->translate($handle, $translations->domain(), $translations->path() ?? '');
    }

    /**
     * @param non-empty-string $handle
     */
    private function enqueue(string $handle, Condition $condition): void
    {
        if (!$condition->isSatisfied()) {
            return;
        }

        $this->registry->enqueue($handle);
    }
}
