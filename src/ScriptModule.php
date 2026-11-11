<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;

/**
 * A script module, an ES module loaded with an import map, like the view script of a block.
 */
final readonly class ScriptModule
{
    /** @var non-empty-string */
    private string $source;

    /**
     * @param Handle $id The id of the module, the name other modules import, like @my-plugin/view.
     * @param string $source The URL.
     * @param Dependencies $dependencies The ids of the modules it imports.
     * @param Translations|null $translations Needs WordPress 7.0.
     */
    public function __construct(
        private Handle $id,
        string $source,
        private Dependencies $dependencies = new Dependencies(),
        private Version $version = new CoreVersion(),
        private ?Translations $translations = null,
        private Condition $condition = new Always(),
    ) {
        $this->source = $this->checked($source);
    }

    public function id(): Handle
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function source(): string
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

    public function translations(): ?Translations
    {
        return $this->translations;
    }

    public function condition(): Condition
    {
        return $this->condition;
    }

    /**
     * @return non-empty-string
     */
    private function checked(string $source): string
    {
        $source = \trim($source);
        if ($source !== '') {
            return $source;
        }

        throw new InvalidArgumentException(\sprintf(
            'The source of the script module "%s" must not be empty',
            $this->id->value()
        ));
    }
}
