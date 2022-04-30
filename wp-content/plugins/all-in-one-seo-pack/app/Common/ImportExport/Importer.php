<?php
namespace AIOSEO\Plugin\Common\ImportExport;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Imports the settings and meta data from other plugins.
 *
 * @since 4.0.0
 */
abstract class Importer {
	/**
	 * Starts the import.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $options What the user wants to import.
	 * @return void
	 */
	public function doImport( $options = [] ) {
		if ( empty( $options ) ) {
			$this->importSettings();
			$this->importPostMeta();
			$this->importTermMeta();

			return;
		}

		foreach ( $options as $optionName ) {
			switch ( $optionName ) {
				case 'settings':
					$this->importSettings();
					break;
				case 'postMeta':
					$this->postMeta->scheduleImport();
					break;
				default:
					break;
			}
		}
	}
}