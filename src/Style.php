<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;

/**
 * A style to register, with the inline styles printed after it.
 */
final readonly class Style
{
    /** @var non-empty-string|null */
    private ?string $source;

    /**
     * @param string|null $source The URL, null for a style with only the inline styles, not empty.
     * @param list<string> $inlineStyles
     * @param array<'alt'|'path'|'rtl'|'suffix'|'title', string|bool> $data The extra data of the style, like rtl.
     */
    public function __construct(
        private Handle $handle,
        ?string $source,
        private Dependencies $dependencies = new Dependencies(),
        private Version $version = new CoreVersion(),
        private string $media = 'all',
        private array $inlineStyles = [],
        private array $data = [],
        private Condition $condition = new Always(),
    ) {
        $this->source = $this->checked($source);
        $this->assertNotEmpty($media, 'media');
        foreach ($inlineStyles as $inlineStyle) {
            $this->assertNotEmpty(\trim($inlineStyle), 'inline style');
        }
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

    public function media(): string
    {
        return $this->media;
    }

    /**
     * @return list<string>
     */
    public function inlineStyles(): array
    {
        return $this->inlineStyles;
    }

    /**
     * @return array<'alt'|'path'|'rtl'|'suffix'|'title', string|bool>
     */
    public function data(): array
    {
        return $this->data;
    }

    public function condition(): Condition
    {
        return $this->condition;
    }

    /**
     * @return non-empty-string|null
     */
    private function checked(?string $source): ?string
    {
        if ($source !== '') {
            return $source;
        }

        throw new InvalidArgumentException(\sprintf(
            'The source of the style "%s" must not be empty, use null for only the inline styles',
            $this->handle->value()
        ));
    }

    private function assertNotEmpty(string $value, string $name): void
    {
        if ($value !== '') {
            return;
        }

        throw new InvalidArgumentException(\sprintf(
            'The %s of the style "%s" must not be empty',
            $name,
            $this->handle->value()
        ));
    }
}
