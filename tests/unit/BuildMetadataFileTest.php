<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\BuildMetadataFile;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use SplFileInfo;

/**
 * The .asset.php file the build writes next to the built file.
 */
final class BuildMetadataFileTest extends Unit
{
    #[Test]
    public function itReadsTheDependenciesAndTheVersion(): void
    {
        $metadata = (new BuildMetadataFile($this->file('build/index.js')))->read();

        $this->assertSame(['wp-dom-ready', 'wp-i18n'], $metadata->dependencies()->toArray());
        $this->assertSame('a1b2c3d4e5f6a7b8c9d0', $metadata->version()->value());
    }

    #[Test]
    public function theStyleSharesTheFileOfTheScript(): void
    {
        $metadata = (new BuildMetadataFile($this->file('build/index.css')))->read();

        $this->assertSame('a1b2c3d4e5f6a7b8c9d0', $metadata->version()->value());
    }

    #[Test]
    public function itTellsWhenTheAssetsAreBuilt(): void
    {
        $this->assertTrue((new BuildMetadataFile($this->file('build/index.js')))->exists());
        $this->assertFalse((new BuildMetadataFile($this->file('build/without-metadata.js')))->exists());
    }

    #[Test]
    public function itThrowsWhenTheFileIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('without-metadata.asset.php');
        (new BuildMetadataFile($this->file('build/without-metadata.js')))->read();
    }

    #[Test]
    public function itThrowsWhenTheFileIsNotAnArray(): void
    {
        $this->expectException(RuntimeException::class);
        (new BuildMetadataFile($this->file('build/invalid.js')))->read();
    }

    #[Test]
    public function itThrowsWhenTheVersionIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        (new BuildMetadataFile($this->file('build/no-version.js')))->read();
    }

    #[Test]
    public function itAcceptsTheFilesOfTheFinder(): void
    {
        $fromFinder = new class (\codecept_data_dir('build/index.js')) extends SplFileInfo {
        };

        $this->assertTrue((new BuildMetadataFile($fromFinder))->exists());
    }

    #[Test]
    public function itKeepsOnlyTheHandlesOfTheDependencies(): void
    {
        $metadata = (new BuildMetadataFile($this->file('build/mixed-dependencies.js')))->read();

        $this->assertSame(['wp-i18n', 'jquery'], $metadata->dependencies()->toArray());
    }

    #[Test]
    public function itHasNoDependenciesWhenTheyAreNotAList(): void
    {
        $metadata = (new BuildMetadataFile($this->file('build/broken-dependencies.js')))->read();

        $this->assertSame([], $metadata->dependencies()->toArray());
    }

    private function file(string $name): SplFileInfo
    {
        return new SplFileInfo((string) \codecept_data_dir($name));
    }
}
