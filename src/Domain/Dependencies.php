<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * The handles an asset loads after, in order and without duplicates.
 */
final readonly class Dependencies
{
    /** @var list<non-empty-string> */
    private array $handles;

    public function __construct(string ...$handles)
    {
        $values = [];
        foreach ($handles as $handle) {
            $values[] = (new Handle($handle))->value();
        }

        $this->handles = \array_values(\array_unique($values));
    }

    /**
     * A copy with the other dependencies after these ones.
     */
    public function with(Dependencies $other): self
    {
        return new self(...$this->handles, ...$other->toArray());
    }

    /**
     * @return list<non-empty-string>
     */
    public function toArray(): array
    {
        return $this->handles;
    }
}
