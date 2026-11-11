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

    public function is(Handle $handle, AssetStatus $status): bool
    {
        return $this->registry->is($handle->value(), $status);
    }

    /**
     * Prints the registered handles now, with their dependencies, like a script registered with RegisterOnly
     * that must run at a given point of the page. A handle not registered is skipped.
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
     * Prints the data in a global object before the script, for the scripts that still read it there.
     *
     * @param non-empty-string $objectName
     * @param array<string, mixed> $data
     */
    public function localize(Handle $handle, string $objectName, array $data): void
    {
        $this->registry->localize($handle->value(), $objectName, $data);
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
