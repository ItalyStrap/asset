<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Where the inline code is printed, before or after the script.
 */
enum Position: string
{
    case Before = 'before';
    case After = 'after';
}
