<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit\Domain;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Domain\FixedVersion;
use ItalyStrap\Asset\Domain\NoVersion;
use ItalyStrap\Asset\Domain\WordPressVersion;
use PHPUnit\Framework\Attributes\Test;

/**
 * WordPress reads false as its own version and null as no version in the URL.
 */
final class VersionTest extends Unit
{
    #[Test]
    public function theWordPressVersionIsFalse(): void
    {
        $this->assertFalse((new WordPressVersion())->value());
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
