<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\AssetStatusInterface;
use ItalyStrap\Asset\Script;
use Prophecy\Argument;

if ( ! \class_exists( \ItalyStrap\Asset\Test\BaseAsset::class ) ) {
	require 'BaseAsset.php';
}

class ScriptTest extends BaseAsset
{
	protected function _before()
	{
		parent::_before();
		$this->type = 'script';
		$this->in_footer_or_media = true;
	}

	/**
	 * @return Asset
	 * @throws \ReflectionException
	 */
	protected function getInstance()
	{
		$sut = new Script( $this->getFile(), $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( Script::class, $sut, '' );
	}
}