<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

interface AssetInterface
{
    public function handle(): string;

    public function location(): string;

    public function isEnqueued(): bool;

    public function isRegistered(): bool;

    public function shouldEnqueue(): bool;

    /**
     * Register the script
     */
    public function register(): bool;

    /**
     * Enqueue the script
     */
    public function enqueue(): void;
}
