<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

/**
 * Child class API for Script
 */
final class Script extends Asset {

	public const EXTENSION		= 'js';
	public const OBJECT_NAME	= 'object_name';
	public const PARAMS			= 'params';

	/**
	 * @inheritDoc
	 */
	public function register(): bool {
		/** @var bool $registered */
		$registered = \wp_register_script(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::IN_FOOTER, false)
		);

		return \boolval( $registered );
	}

	/**
	 * @inheritDoc
	 */
	public function enqueue(): void {
		\wp_enqueue_script(
			$this->handle,
			$this->config->get( Asset::URL ),
			$this->config->get(Asset::DEPENDENCIES, []),
			$this->config->get( Asset::VERSION ),
			$this->config->get(Asset::IN_FOOTER, false)
		);

		$this->shouldLocalize();
	}

	/**
	 * Localize the script
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return bool
	 */
	private function localizeScript(): bool {
		/** @var bool $localized */
		$localized = \wp_localize_script(
			$this->handle,
			$this->config->get( Asset::LOCALIZE . '.' . self::OBJECT_NAME ),
			$this->config->get( Asset::LOCALIZE . '.' . self::PARAMS )
		);

		return \boolval( $localized );
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
