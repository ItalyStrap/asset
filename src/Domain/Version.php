<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * The version WordPress appends to the URL as the ver query argument.
 */
interface Version
{
    /**
     * False for the WordPress version, null for no version.
     */
    public function value(): string|false|null;
}
