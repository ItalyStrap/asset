<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\AssetStatusInterface;

if ( ! \class_exists( \ItalyStrap\Asset\Test\BaseAsset::class ) ) {
	require 'BaseAsset.php';
}

class StyleTest extends BaseAsset
{
	/**
	 * @return AssetStatusInterface
	 */
	protected function getInstance()
	{
		$sut = new \ItalyStrap\Asset\Style( $this->getFile(), $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( \ItalyStrap\Asset\Style::class, $sut, '' );
	}
}