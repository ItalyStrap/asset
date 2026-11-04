<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

use InvalidArgumentException;
use SplFileInfo;

/**
 * The text domain of the script strings and the folder of its JSON translations.
 */
final readonly class Translations
{
    /**
     * @param SplFileInfo|null $folder The folder of the JSON files, null for the languages folder of the core.
     */
    public function __construct(
        private string $domain,
        private ?SplFileInfo $folder = null,
    ) {
        if ($domain === '') {
            throw new InvalidArgumentException('The text domain must not be empty');
        }
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function path(): ?string
    {
        return $this->folder?->getPathname();
    }
}
