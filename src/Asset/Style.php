<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Child class API for Style
 */
final class Style extends Asset {

	/**
	 * Pre register the style
	 * @return bool
	 */
	public function register(): bool {
		return \wp_register_style(
			$this->handle,
			$this->file->url(),
			$this->config->get('deps', []),
			$this->file->version(),
			$this->config->get('media', 'all')
		);
	}

	/**
	 * Enqueue the style
	 */
	public function enqueue(): void {
		\wp_enqueue_style(
			$this->handle,
			$this->file->url(),
			$this->config->get('deps', []),
			$this->file->version(),
			$this->config->get('media', 'all')
		);
	}
}
