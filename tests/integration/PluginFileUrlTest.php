<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Integration;

use InvalidArgumentException;
use ItalyStrap\Asset\Core\PluginFileUrl;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;

final class PluginFileUrlTest extends WPTestCase
{
    private const LINK = 'asset-fixture-link';

    protected function tearDown(): void
    {
        $link = \WP_PLUGIN_DIR . '/' . self::LINK;
        if (\is_link($link)) {
            \unlink($link);
        }

        $GLOBALS['wp_plugin_paths'] = [];
        parent::tearDown();
    }

    #[Test]
    public function itHasTheUrlOfTheFileInThePlugin(): void
    {
        $url = new PluginFileUrl($this->file('plugin/plugin.php'));

        $this->assertSame(
            \content_url('/plugins/asset/tests/_data/plugin/build/index.js'),
            $url->url($this->file('plugin/build/index.js'))
        );
    }

    #[Test]
    public function itHasTheUrlOfTheSymlinkedPlugin(): void
    {
        $link = \WP_PLUGIN_DIR . '/' . self::LINK;
        \symlink((string) \codecept_data_dir('plugin'), $link);
        // The core registers the real path of each active plugin while it loads it
        \wp_register_plugin_realpath($link . '/plugin.php');

        $url = new PluginFileUrl($this->file('plugin/plugin.php'));

        $this->assertSame(
            \content_url('/plugins/' . self::LINK . '/build/index.js'),
            $url->url(new SplFileInfo($link . '/build/index.js'))
        );
    }

    #[Test]
    public function itRejectsAFileOutsideThePlugin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('build/index.js');

        (new PluginFileUrl($this->file('plugin/plugin.php')))->url($this->file('build/index.js'));
    }

    private function file(string $name): SplFileInfo
    {
        return new SplFileInfo((string) \codecept_data_dir($name));
    }
}
