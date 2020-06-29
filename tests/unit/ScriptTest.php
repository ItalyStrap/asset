<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Asset\AssetStatusInterface;
use Prophecy\Argument;

if ( ! \class_exists( \ItalyStrap\Asset\Test\BaseAsset::class ) ) {
	require 'BaseAsset.php';
}

class ScriptTest extends BaseAsset
{
	/**
	 * @return AssetStatusInterface
	 */
	protected function getInstance()
	{
		$sut = new \ItalyStrap\Asset\Script( $this->getFile(), $this->getConfig() );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = parent::instanceOk();
		$this->assertInstanceOf( \ItalyStrap\Asset\Script::class, $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {
		$this->config->has(Argument::type('string'))->willReturn(true);
		$this->config->handle = 'handle';
		$this->config->get( Argument::type('string'), Argument::any() )->will(
			function () {
				return '';
			}
		);

		$this->file->url()->willReturn('url');
		$this->file->version()->willReturn(42);

		\tad\FunctionMockerLe\define( 'wp_register_script', function (
			$handle,
			$src,
			$deps = array(),
			$ver = false,
			$in_footer = false ) {
    		return true;
		} );

		$sut = $this->getInstance();
		$sut->register();
	}

}