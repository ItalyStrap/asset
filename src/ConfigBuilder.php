<?php
declare(strict_types=1);

namespace ItalyStrap\Asset;

use ItalyStrap\Config\ConfigFactory;

class ConfigBuilder {

	/**
	 * @var array
	 */
	private $config = [];

	private $schema = [
		'handle'		=> '',
		'file'			=> '',
		'deps'			=> [],
		'version'		=> '',
		'in_footer'		=> true,
	];

	private $types = [
		'css'	=> \Inpsyde\Assets\Style::class,
		'js'	=> \Inpsyde\Assets\Script::class
	];

	/**
	 * @param array ...$configs
	 */
	public function addConfig( array $configs ) {
		$this->config = \array_merge( $this->config, $configs );
	}

	/**
	 * https://paulund.co.uk/get-the-file-extension-in-php
	 * @param string $file
	 * @return string
	 */
	private function fileExtension( string $file ): string {
		$array = explode( ".", $file );
		return end( $array );
	}

	public function parsedConfig(): \Generator {

		foreach ( $this->config as $config ) {
			$config['url'] = $config['file'] = $config['file'] ?? '';

			if ( 'comment-reply' === $config[ Asset::HANDLE ] ) {
				$config['file'] = 'comment-reply.js';
			}

			$config['type'] = $this->types[ $this->fileExtension( $config['file'] ) ];
			$config['enqueue'] = $config['load_on'] ?? true;
			$config['dependencies'] = $config['deps'] ?? [];

			yield ConfigFactory::make($config);
		}
	}
}
