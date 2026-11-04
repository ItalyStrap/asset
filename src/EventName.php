<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * The names of the events where the core enqueues the assets.
 */
final class EventName
{
    public const FRONT_END = 'wp_enqueue_scripts';
    public const ADMIN = 'admin_enqueue_scripts';
    /** The editor interface, not the content in the iframe. */
    public const EDITOR = 'enqueue_block_editor_assets';
    /** The content of the blocks, in the front end and in the editor iframe. */
    public const BLOCK = 'enqueue_block_assets';
    public const LOGIN = 'login_enqueue_scripts';

    private function __construct()
    {
    }
}
