<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Where the script is printed and with which strategy.
 */
final readonly class ScriptLoading
{
    public function __construct(
        private bool $inFooter = false,
        private ?Strategy $strategy = null,
    ) {
    }

    /**
     * The $args of the script register and enqueue functions.
     *
     * @return array{in_footer: bool, strategy?: value-of<Strategy>}
     */
    public function toArray(): array
    {
        if (!$this->strategy instanceof Strategy) {
            return ['in_footer' => $this->inFooter];
        }

        return ['in_footer' => $this->inFooter, 'strategy' => $this->strategy->value];
    }
}
