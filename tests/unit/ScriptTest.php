<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Script;

use function tad\FunctionMockerLe\define;

// phpcs:disable
include_once __DIR__ . '/UnitBaseAsset.php';
// phpcs:enable

class ScriptTest extends UnitBaseAsset
{
	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        parent::_before();
        $this->type = 'script';
        $this->in_footer_or_media = true;
    }

    /**
     * @return Asset
     * @throws \ReflectionException
     */
    protected function getInstance(): Script
    {
        return new Script($this->getConfig());
    }

    /**
     * @test
     */
    public function instanceOk(): void
    {
        $sut = parent::instanceOk();
        $this->assertInstanceOf(Script::class, $sut, '');
    }

    /**
     * @test
     */
    public function itShouldAddLocalizedScript(): void
    {

        define('wp_enqueue_script', function (): void {
        });

        $wp_localize_script_called = 0;
        define(
            'wp_localize_script',
            function (string $handle, string $object_name, array $l10n) use (&$wp_localize_script_called): true {
                $this->assertStringContainsString('handle', $handle, '');
                $this->assertStringContainsString('object_name', $object_name, '');
                $this->assertArrayHasKey('key', $l10n, '');
                $this->assertSame(42, $l10n['key']);
                $wp_localize_script_called++;
                return true;
            }
        );

        $this->config->has(Asset::LOCALIZE)->willReturn(true);

        $this->config->get(Asset::HANDLE)->willReturn('handle');
        $this->config->get(Asset::URL)->willReturn('url');
        $this->config->get(Asset::DEPENDENCIES, [])->willReturn([]);
        $this->config->get(Asset::VERSION)->willReturn(1);
        $this->config->get('in_footer', false)->willReturn(true);
        $this->config->get("localize.object_name")->willReturn('object_name');
        $this->config->get("localize.params")->willReturn(['key' => 42]);

        $sut = $this->getInstance();

        $sut->enqueue();
        $this->assertSame(1, $wp_localize_script_called, 'Sould be called once');
    }
}
