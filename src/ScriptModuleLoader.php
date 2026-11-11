<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Asset\Core\CoreScriptModuleRegistry;

/**
 * Registers a script module with its translations, then enqueues it when its condition is satisfied.
 */
final readonly class ScriptModuleLoader
{
    public function __construct(
        private ScriptModuleRegistry $registry = new CoreScriptModuleRegistry(),
    ) {
    }

    public function load(ScriptModule $module): void
    {
        $id = $module->id()->value();

        $this->registry->register(
            $id,
            $module->source(),
            $module->dependencies()->toArray(),
            $module->version()->value()
        );

        $this->translate($id, $module->translations());
        $this->enqueue($id, $module->condition());
    }

    public function dequeue(Handle $id): void
    {
        $this->registry->dequeue($id->value());
    }

    public function deregister(Handle $id): void
    {
        $this->registry->deregister($id->value());
    }

    /**
     * @param non-empty-string $id
     */
    private function translate(string $id, ?Translations $translations): void
    {
        if (!$translations instanceof Translations) {
            return;
        }

        $this->registry->translate($id, $translations->domain(), $translations->path() ?? '');
    }

    /**
     * @param non-empty-string $id
     */
    private function enqueue(string $id, Condition $condition): void
    {
        if (!$condition->isSatisfied()) {
            return;
        }

        $this->registry->enqueue($id);
    }
}
