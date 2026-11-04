<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Core;

use ItalyStrap\Asset\FileUrl;
use ItalyStrap\Asset\FolderUrls;
use SplFileInfo;

/**
 * The URL of a file of the active theme, the child theme or its parent.
 */
final readonly class ThemeFileUrl implements FileUrl
{
    public function url(SplFileInfo $file): string
    {
        return (new FolderUrls([
            \get_stylesheet_directory() => \get_stylesheet_directory_uri(),
            \get_template_directory() => \get_template_directory_uri(),
        ]))->url($file);
    }
}
