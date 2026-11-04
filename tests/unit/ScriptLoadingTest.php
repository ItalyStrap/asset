<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Tests\Unit;

use Codeception\Test\Unit;
use ItalyStrap\Asset\ScriptLoading;
use ItalyStrap\Asset\Strategy;
use PHPUnit\Framework\Attributes\Test;

final class ScriptLoadingTest extends Unit
{
    #[Test]
    public function itLoadsInTheHeadWithoutStrategyByDefault(): void
    {
        $this->assertSame(['in_footer' => false], (new ScriptLoading())->toArray());
    }

    #[Test]
    public function itLoadsInTheFooter(): void
    {
        $this->assertSame(['in_footer' => true], (new ScriptLoading(inFooter: true))->toArray());
    }

    #[Test]
    public function itHasTheStrategy(): void
    {
        $this->assertSame(
            ['in_footer' => true, 'strategy' => 'defer'],
            (new ScriptLoading(inFooter: true, strategy: Strategy::Defer))->toArray()
        );
        $this->assertSame(
            ['in_footer' => false, 'strategy' => 'async'],
            (new ScriptLoading(strategy: Strategy::Async))->toArray()
        );
    }
}
