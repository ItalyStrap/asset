<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

use InvalidArgumentException;

/**
 * The unique name of a script or a style in WordPress.
 */
final readonly class Handle
{
    /** @var non-empty-string */
    private string $value;

    public function __construct(string $value)
    {
        $value = \trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('The handle must not be empty');
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
