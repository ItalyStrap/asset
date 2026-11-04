<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * The version appended to the URL as the ver query argument.
 */
interface Version
{
    /**
     * False for the core version, null for no version.
     */
    public function value(): string|false|null;
}
