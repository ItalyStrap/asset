<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * How the browser loads a script without blocking the page.
 */
enum Strategy: string
{
    case Defer = 'defer';
    case Async = 'async';
}
