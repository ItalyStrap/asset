<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Child class API for Style
 */
final class Style extends Asset {

	public const EXTENSION = 'css';

	/**
	 * Pre register the style
	 * @return bool
	 */
	public function register(): bool {
		/** @var bool $registered */
		$registered = \wp_register_style(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::MEDIA, 'all')
		);

		return \boolval( $registered );
	}

	/**
	 * Enqueue the style
	 */
	public function enqueue(): void {
		\wp_enqueue_style(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::MEDIA, 'all')
		);
	}
}
