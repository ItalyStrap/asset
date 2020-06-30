<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\Style;

if ( ! \class_exists( \ItalyStrap\Asset\Test\BaseAsset::class ) ) {
	require 'BaseAsset.php';
}

class StyleTest extends BaseAsset
{
	protected function _before()
	{
		parent::_before();
		$this->type = 'style';
		$this->in_footer_or_media = 'all';
	}

	/**
	 * @return Asset
	 */
	protected function getInstance()
	{
		$sut = new Style( $this->getFile(), $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( Style::class, $sut, '' );
	}
}