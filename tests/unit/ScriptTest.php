<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Script;

// phpcs:disable
include_once 'UnitBaseAsset.php';
// phpcs:enable

class ScriptTest extends UnitBaseAsset {

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
	protected function getInstance() {
		$sut = new Script( $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( Script::class, $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldAddLocalizedScript() {

		$called = 0;
		\tad\FunctionMockerLe\define(
			'wp_localize_script',
			function ( string $handle, string $object_name, array $l10n ) use ( &$called ) {
				$this->assertStringContainsString('handle', $handle, '');
				$this->assertStringContainsString('object_name', $object_name, '');
				$this->assertArrayHasKey('key', $l10n, '');
				$this->assertSame(42, $l10n['key']);
				$called++;
				return true;
			}
		);

		$this->config->get("localize.object_name")->willReturn('object_name');
		$this->config->get("localize.params")->willReturn(['key' => 42]);

		$sut = $this->getInstance();

		$sut->localizeScript();
	}
}
