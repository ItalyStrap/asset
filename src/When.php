<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * The asset is enqueued when the callback returns true, it runs at the enqueue, when the query is ready.
 */
final readonly class When implements Condition
{
    /** @var callable(): mixed */
    private mixed $callback;

    /**
     * @param callable(): mixed $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function isSatisfied(): bool
    {
        return (bool) ($this->callback)();
    }
}
