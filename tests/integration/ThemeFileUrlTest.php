<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use InvalidArgumentException;
use ItalyStrap\Asset\Core\ThemeFileUrl;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class ThemeFileUrlTest extends WPTestCase
{
    private string $previousTheme = '';

    protected function setUp(): void
    {
        parent::setUp();
        \register_theme_directory((string) \codecept_data_dir('themes'));
        \search_theme_directories(true);
        $this->previousTheme = \get_stylesheet();
        \switch_theme('child-theme');
    }

    protected function tearDown(): void
    {
        \switch_theme($this->previousTheme);
        parent::tearDown();
    }

    #[Test]
    public function itHasTheUrlOfTheFileInTheChildTheme(): void
    {
        $this->assertSame(
            \get_stylesheet_directory_uri() . '/build/child.js',
            (new ThemeFileUrl())->url($this->file('themes/child-theme/build/child.js'))
        );
    }

    #[Test]
    public function itHasTheUrlOfTheFileInTheParentTheme(): void
    {
        $this->assertSame(
            \get_template_directory_uri() . '/build/parent.js',
            (new ThemeFileUrl())->url($this->file('themes/parent-theme/build/parent.js'))
        );
    }

    #[Test]
    public function itRejectsAFileOutsideTheThemes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ThemeFileUrl())->url($this->file('build/index.js'));
    }

    private function file(string $name): SplFileInfo
    {
        return new SplFileInfo((string) \codecept_data_dir($name));
    }
}
