<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use cli\arguments\Argument;
use Codeception\Test\Unit;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Version\VersionInterface;
use SplFileInfo;

class FileTest extends Unit
{
    use \Prophecy\PhpUnit\ProphecyTrait;


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
    public function getVersion(): VersionInterface
    {
        return $this->version->reveal();
    }

    /**
     * @return SplFileInfo
     */
    public function getFakeFile(): SplFileInfo
    {
        return $this->fakeFile->reveal();
    }

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->file = new SplFileInfo(\codecept_data_dir('fixtures/parent/css') . '/style.css');
        $this->fakeFile = $this->prophesize(SplFileInfo::class);
        $this->version = $this->prophesize(VersionInterface::class);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    /**
     * @return File
     */
    public function getInstance()
    {
        $sut = new File($this->getFakeFile(), $this->getVersion(), $this->base_url, $this->base_path);
        $this->assertInstanceOf(File::class, $sut, '');
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

        $sut = $this->getInstance();
        $this->assertEquals(\strval($expected), $sut->version(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnTheVersionFromMock()
    {
        $expected = '42';

        $this->version->version(\Prophecy\Argument::any(), \Prophecy\Argument::any())->willReturn($expected);

        $sut = $this->getInstance();
        $this->assertEquals(\strval($expected), $sut->version(), '');
    }

    public static function baseStringProvider()
    {
        return [
            'Without Slash' => [
                $_SERVER['WP_ROOT_FOLDER'],
                'http://wordpress',
            ],
            'Test2' => [
                $_SERVER['WP_ROOT_FOLDER'] . '/',
                'http://wordpress',
            ],
            'Test3' => [
                $_SERVER['WP_ROOT_FOLDER'],
                'http://wordpress' . '/',
            ],
            'Test4' => [
                $_SERVER['WP_ROOT_FOLDER'] . '\\',
                'http://wordpress',
            ],
            'Test5' => [
                $_SERVER['WP_ROOT_FOLDER'],
                'http://wordpress' . '\\',
            ],
            'Test6' => [
                $_SERVER['WP_ROOT_FOLDER'] . '/',
                'http://wordpress' . '\\',
            ],
            'Test7' => [
                $_SERVER['WP_ROOT_FOLDER'] . '\\',
                'http://wordpress' . '/',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider baseStringProvider
     */
    public function itShouldReturnTheFileUrl($base_path, $base_url)
    {
        $this->base_path = $base_path;
        $this->base_url = $base_url;
        $this->fakeFile->getRealPath()->willReturn(
            \codecept_data_dir('fixtures/parent/css') . '\style.css'
        );

        $expected = \rtrim($base_url, '\/') . '/wp-content/plugins/asset/tests/_data/fixtures/parent/css/style.css';
        $sut = $this->getInstance();
        $this->assertEquals($expected, $sut->url(), '');
    }
}
