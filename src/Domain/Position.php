<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * Where WordPress prints the inline code, before or after the script.
 */
enum Position: string
{
    case Before = 'before';
    case After = 'after';
}
