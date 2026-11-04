<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;

/**
 * A script to register, with everything printed with it.
 */
final readonly class Script
{
    /** @var non-empty-string|null */
    private ?string $source;

    /**
     * @param string|null $source The URL, null for a handle that only groups its dependencies, not empty.
     * @param list<InlineScript> $inlineScripts
     * @param array<non-empty-string, string|bool> $data The extra data of the script, like a consent category.
     */
    public function __construct(
        private Handle $handle,
        ?string $source,
        private Dependencies $dependencies = new Dependencies(),
        private Version $version = new CoreVersion(),
        private ScriptLoading $loading = new ScriptLoading(),
        private array $inlineScripts = [],
        private array $data = [],
        private ?Translations $translations = null,
        private Condition $condition = new Always(),
    ) {
        $this->source = $this->checked($source);
    }

    public function handle(): Handle
    {
        return $this->handle;
    }

    /**
     * @return non-empty-string|null
     */
    public function source(): ?string
    {
        return $this->source;
    }

    public function dependencies(): Dependencies
    {
        return $this->dependencies;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function loading(): ScriptLoading
    {
        return $this->loading;
    }

    /**
     * @return list<InlineScript>
     */
    public function inlineScripts(): array
    {
        return $this->inlineScripts;
    }

    /**
     * @return array<non-empty-string, string|bool>
     */
    public function data(): array
    {
        return $this->data;
    }

    public function translations(): ?Translations
    {
        return $this->translations;
    }

    public function condition(): Condition
    {
        return $this->condition;
    }

    /**
     * An empty URL would print a script without source.
     *
     * @return non-empty-string|null
     */
    private function checked(?string $source): ?string
    {
        if ($source !== '') {
            return $source;
        }

        throw new InvalidArgumentException(\sprintf(
            'The source of the script "%s" must not be empty, use null for no file',
            $this->handle->value()
        ));
    }
}
