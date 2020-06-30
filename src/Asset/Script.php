<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Child class API for Script
 */
final class Script extends Asset {

	/**
	 * Pre register the script
	 * @param array $config
	 * @return bool
	 */
	public function register(): bool {
		return \wp_register_script(
			$this->handle,
			$this->file->url(),
			$this->config->get('deps', []),
			$this->file->version(),
			$this->config->get('in_footer', false)
		);
	}

	/**
	 * Enqueue the script
	 */
	public function enqueue(): void {
		\wp_enqueue_script(
			$this->handle,
			$this->file->url(),
			$this->config->get('deps', []),
			$this->file->version(),
			$this->config->get('in_footer', false)
		);
	}

	/**
	 * Localize the script
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return null
	 */
//	protected function addInlineScript( array $config = array() ) {
//
//		return \wp_add_inline_script(
//			$this->handle,
//			$config['data'],
//			$config['position']
//		);
//	}

	/**
	 * Localize the script
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return null
	 */
//	protected function localizeScript( array $config = array() ) {
//
//		return \wp_localize_script(
//			$this->handle,
//			$config['localize']['object_name'],
//			$config['localize']['params']
//		);
//	}
}
