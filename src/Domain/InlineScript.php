<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

use InvalidArgumentException;

/**
 * The code printed next to a script, like the data the script reads.
 */
final readonly class InlineScript
{
    public function __construct(
        private string $code,
        private Position $position = Position::After,
    ) {
        if (\trim($code) === '') {
            throw new InvalidArgumentException('The inline script must not be empty');
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function position(): Position
    {
        return $this->position;
    }
}
