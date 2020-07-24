<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Child class API for Script
 */
final class Script extends Asset {

	/**
	 * @inheritDoc
	 */
	public function register(): bool {
		$this->shouldLocalize();
		return \wp_register_script(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::IN_FOOTER, false)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function enqueue(): void {
		$this->shouldLocalize();
		\wp_enqueue_script(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::IN_FOOTER, false)
		);
	}

	/**
	 * Localize the script
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return bool
	 */
	public function localizeScript(): bool {
		return \wp_localize_script(
			$this->handle,
			$this->config->get( Asset::LOCALIZE . '.object_name' ),
			$this->config->get( Asset::LOCALIZE . '.params' )
		);
	}

	private function shouldLocalize(): void {
		if ( $this->config->has( Asset::LOCALIZE ) ) {
			$this->localizeScript();
		}
	}

	/**
	 * Localize the script
	 * @TODO Add inline script
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
}
