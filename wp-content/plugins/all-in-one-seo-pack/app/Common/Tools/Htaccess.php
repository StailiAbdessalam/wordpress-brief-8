<?php
namespace AIOSEO\Plugin\Common\Tools;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Htaccess {
	/**
	 * The path to the .htaccess file.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	private $path = '';

	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->path = ABSPATH . '.htaccess';
	}

	/**
	 * Get the contents of the .htaccess file.
	 *
	 * @since 4.0.0
	 *
	 * @return string The contents of the file.
	 */
	public function getContents() {
		$fs = aioseo()->core->fs;
		if ( ! $fs->exists( $this->path ) ) {
			return false;
		}

		$contents = $fs->getContents( $this->path );

		return aioseo()->helpers->encodeOutputHtml( $contents );
	}

	/**
	 * Saves the contents of the .htaccess file.
	 *
	 * @since 4.0.0
	 *
	 * @param  string  $contents The contents to write.
	 * @return boolean           True if the file was updated.
	 */
	public function saveContents( $contents ) {
		$fs         = aioseo()->core->fs;
		$fileExists = $fs->exists( $this->path );
		if ( ! $fileExists || $fs->isWritable( $this->path ) ) {
			$success = $fs->putContents( $this->path, $contents );
			if ( false === $success ) {
				return false;
			}

			return true;
		}

		return false;
	}
}