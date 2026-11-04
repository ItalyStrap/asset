<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * The version of WordPress, the default of the WordPress functions.
 */
final readonly class WordPressVersion implements Version
{
    public function value(): false
    {
        return false;
    }
}
