<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\FixedVersion;
use ItalyStrap\Asset\NoVersion;
use ItalyStrap\Asset\CoreVersion;
use PHPUnit\Framework\Attributes\Test;

/**
 * The register functions read false as the core version and null as no version in the URL.
 */
final class VersionTest extends Unit
{
    #[Test]
    public function theCoreVersionIsFalse(): void
    {
        $this->assertFalse((new CoreVersion())->value());
    }

    #[Test]
    public function noVersionIsNull(): void
    {
        $this->assertNull((new NoVersion())->value());
    }

    #[Test]
    public function aFixedVersionIsItsString(): void
    {
        $this->assertSame('1.2.3', (new FixedVersion('1.2.3'))->value());
    }

    #[Test]
    public function aFixedVersionIsNotEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FixedVersion('');
    }
}
