<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

use InvalidArgumentException;

/**
 * A given version, like the hash of the build or the version of the plugin.
 */
final readonly class FixedVersion implements Version
{
    /** @var non-empty-string */
    private string $value;

    public function __construct(string $value)
    {
        if ($value === '') {
            throw new InvalidArgumentException('The version must not be empty');
        }

        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
