<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Asset\Dependencies;
use PHPUnit\Framework\Attributes\Test;

final class DependenciesTest extends Unit
{
    #[Test]
    public function itIsEmptyByDefault(): void
    {
        $this->assertSame([], (new Dependencies())->toArray());
    }

    #[Test]
    public function itKeepsTheHandlesInOrderWithoutDuplicates(): void
    {
        $this->assertSame(
            ['wp-i18n', 'jquery'],
            (new Dependencies('wp-i18n', 'jquery', 'wp-i18n'))->toArray()
        );
    }

    #[Test]
    public function itAddsTheOtherDependenciesAfterItsOwn(): void
    {
        $dependencies = new Dependencies('wp-i18n', 'jquery');

        $merged = $dependencies->with(new Dependencies('jquery', 'my-consent'));

        $this->assertSame(['wp-i18n', 'jquery', 'my-consent'], $merged->toArray());
        $this->assertSame(['wp-i18n', 'jquery'], $dependencies->toArray(), 'The original does not change');
    }

    #[Test]
    public function itRejectsAnEmptyHandle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Dependencies('jquery', ' ');
    }
}
