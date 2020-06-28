<?php
declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use Codeception\Test\Unit;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Version\VersionInterface;
use SplFileInfo;

class FileTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * @var SplFileInfo
	 */
	private $file;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $fakeFile;

	private $base_url = '';
	private $base_path = '';
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $version;

	/**
	 * @return VersionInterface
	 */
	public function getVersion(): VersionInterface {
		return $this->version->reveal();
	}

	/**
	 * @return SplFileInfo
	 */
	public function getFakeFile(): SplFileInfo {
		return $this->fakeFile->reveal();
	}

	protected function _before()
    {
    	$this->file = new SplFileInfo( \codecept_data_dir( 'fixtures/parent' ) . '/style.css' );
    	$this->fakeFile = $this->prophesize( SplFileInfo::class );
    	$this->version = $this->prophesize( VersionInterface::class );
    }

    protected function _after()
    {
    }

	/**
	 * @return File
	 */
	public function getInstance() {
		$sut = new File( $this->getFakeFile(), $this->getVersion(), $this->base_url, $this->base_path );
		$this->assertInstanceOf( File::class, $sut, '');
		return $sut;
    }

	/**
	 * @test
	 */
    public function instanceOk()
    {
    	return $this->getInstance();
    }

	/**
	 * @test
	 */
    public function itShouldReturnTheFileVersionFromGetMTIme()
    {
		$expected = time();

    	$this->fakeFile->getMTime()->willReturn($expected);
		$this->version->hasVersion()->willReturn(false);

    	$sut = $this->getInstance();
    	$this->assertEquals( \strval( $expected ), $sut->version(), '' );
    }

	/**
	 * @test
	 */
    public function itShouldReturnTheVersionFromMock()
    {
		$expected = '42';

		$this->version->hasVersion()->willReturn(true);
		$this->version->version()->willReturn('42');

    	$sut = $this->getInstance();
    	$this->assertEquals( \strval( $expected ), $sut->version(), '' );
    }

	public function baseStringProvider() {
		return [
			'Without Slash'	=> [
				$_SERVER['WP_ROOT_FOLDER'],
				$_SERVER['TEST_SITE_WP_URL'],
			],
			'Test2'	=> [
				$_SERVER['WP_ROOT_FOLDER'] . '/',
				$_SERVER['TEST_SITE_WP_URL'],
			],
			'Test3'	=> [
				$_SERVER['WP_ROOT_FOLDER'],
				$_SERVER['TEST_SITE_WP_URL'] . '/',
			],
			'Test4'	=> [
				$_SERVER['WP_ROOT_FOLDER'] . '\\',
				$_SERVER['TEST_SITE_WP_URL'],
			],
			'Test5'	=> [
				$_SERVER['WP_ROOT_FOLDER'],
				$_SERVER['TEST_SITE_WP_URL'] . '\\',
			],
			'Test6'	=> [
				$_SERVER['WP_ROOT_FOLDER'] . '/',
				$_SERVER['TEST_SITE_WP_URL'] . '\\',
			],
			'Test7'	=> [
				$_SERVER['WP_ROOT_FOLDER'] . '\\',
				$_SERVER['TEST_SITE_WP_URL'] . '/',
			],
		];
    }

	/**
	 * @test
	 * @dataProvider baseStringProvider()
	 */
    public function itShouldReturnTheFileUrl( $base_path, $base_url )
    {
    	$this->base_path = $base_path;
    	$this->base_url = $base_url;
		$this->fakeFile->getRealPath()->willReturn($this->file->getRealPath());

		$expected = \rtrim( $base_url, '\/' ) . '/wp-content/plugins/asset/tests/_data/fixtures/parent/style.css';
    	$sut = $this->getInstance();
    	$this->assertEquals( $expected, $sut->url(), '' );
    }

	/**
	 * @test
	 */
    public function file()
    {
//		codecept_debug( $this->file->getPathInfo() );
//		codecept_debug( $this->file->getPath() );
//		codecept_debug( $this->file->getFilename() );
//		codecept_debug( $this->file->getPathname() );
//		codecept_debug( $this->file->getRealPath() );
//		codecept_debug( $this->file->getType() );
//		codecept_debug( $this->file->getBasename() );
//		codecept_debug( $this->file->getATime() );
//		codecept_debug( $this->file->getCTime() );
//		codecept_debug( $this->file->getMTime() );
//		codecept_debug( $this->file->getExtension() );
    }
}