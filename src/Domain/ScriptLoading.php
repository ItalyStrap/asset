<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Domain;

/**
 * Where WordPress prints a script and with which strategy.
 */
final readonly class ScriptLoading
{
    public function __construct(
        private bool $inFooter = false,
        private ?Strategy $strategy = null,
    ) {
    }

    /**
     * The $args of wp_register_script() and wp_enqueue_script().
     *
     * @return array{in_footer: bool, strategy?: value-of<Strategy>}
     */
    public function toArray(): array
    {
        if ($this->strategy === null) {
            return ['in_footer' => $this->inFooter];
        }

        return ['in_footer' => $this->inFooter, 'strategy' => $this->strategy->value];
    }
}
