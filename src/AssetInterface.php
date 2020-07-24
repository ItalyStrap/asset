<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

interface AssetInterface {

	/**
	 * @return string
	 */
	public function location(): string;

	/**
	 * @return bool
	 */
	public function isEnqueued(): bool;

	/**
	 * @return bool
	 */
	public function isRegistered(): bool;

	/**
	 * @return bool
	 */
	public function shouldEnqueue(): bool;

	/**
	 * Register the script
	 * @return bool
	 */
	public function register(): bool;

	/**
	 * Enqueue the script
	 */
	public function enqueue(): void;
}
