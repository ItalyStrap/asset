<?php

namespace ItalyStrap\Asset;

interface FileInterface {

	/**
	 * @return mixed
	 */
	public function version();

	/**
	 * @return string
	 */
	public function url(): string;
}
