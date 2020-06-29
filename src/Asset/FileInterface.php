<?php

namespace ItalyStrap\Asset;

interface FileInterface {

	/**
	 * @return string
	 */
	public function version(): string;

	/**
	 * @return string
	 */
	public function url(): string;
}
