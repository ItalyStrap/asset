<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Infrastructure;

use Codeception\Test\Unit;
use ItalyStrap\Asset\Infrastructure\BuildMetadataFile;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

/**
 * The .asset.php file that @wordpress/scripts writes next to the built file.
 */
final class BuildMetadataFileTest extends Unit
{
    #[Test]
    public function itReadsTheDependenciesAndTheVersion(): void
    {
        $metadata = (new BuildMetadataFile(\codecept_data_dir('build/index.js')))->read();

        $this->assertSame(['wp-dom-ready', 'wp-i18n'], $metadata->dependencies()->toArray());
        $this->assertSame('a1b2c3d4e5f6a7b8c9d0', $metadata->version()->value());
    }

    #[Test]
    public function theStyleSharesTheFileOfTheScript(): void
    {
        $metadata = (new BuildMetadataFile(\codecept_data_dir('build/index.css')))->read();

        $this->assertSame('a1b2c3d4e5f6a7b8c9d0', $metadata->version()->value());
    }

    #[Test]
    public function itTellsWhenTheAssetsAreBuilt(): void
    {
        $this->assertTrue((new BuildMetadataFile(\codecept_data_dir('build/index.js')))->exists());
        $this->assertFalse((new BuildMetadataFile(\codecept_data_dir('build/without-metadata.js')))->exists());
    }

    #[Test]
    public function itThrowsWhenTheFileIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('without-metadata.asset.php');
        (new BuildMetadataFile(\codecept_data_dir('build/without-metadata.js')))->read();
    }

    #[Test]
    public function itThrowsWhenTheFileIsNotAnArray(): void
    {
        $this->expectException(RuntimeException::class);
        (new BuildMetadataFile(\codecept_data_dir('build/invalid.js')))->read();
    }

    #[Test]
    public function itThrowsWhenTheVersionIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        (new BuildMetadataFile(\codecept_data_dir('build/no-version.js')))->read();
    }
}
