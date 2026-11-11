<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * The status of a handle, the values of wp_script_is() and wp_style_is().
 */
enum AssetStatus: string
{
    case Enqueued = 'enqueued';
    case Registered = 'registered';
    /** The same as Enqueued. */
    case Queue = 'queue';
    /** Enqueued, waiting to be printed. */
    case ToDo = 'to_do';
    /** Already printed. */
    case Done = 'done';
}
