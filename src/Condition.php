<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Decides when the asset is enqueued, otherwise it is only registered.
 */
interface Condition
{
    public function isSatisfied(): bool;
}
