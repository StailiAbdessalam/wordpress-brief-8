<?php
namespace AIOSEO\Plugin\Common\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;
use AIOSEO\Plugin\Common\Migration;

/**
 * Route class for the API.
 *
 * @since 4.0.0
 */
class Settings {
	/**
	 * Update the settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function getOptions() {
		return new \WP_REST_Response( [
			'options'  => aioseo()->options->all(),
			'settings' => aioseo()->settings->all()
		], 200 );
	}

	/**
	 * Toggles a card in the settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function toggleCard( $request ) {
		$body  = $request->get_json_params();
		$card  = ! empty( $body['card'] ) ? sanitize_text_field( $body['card'] ) : null;
		$cards = aioseo()->settings->toggledCards;
		if ( array_key_exists( $card, $cards ) ) {
			$cards[ $card ] = ! $cards[ $card ];
			aioseo()->settings->toggledCards = $cards;
		}

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Toggles a radio in the settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function toggleRadio( $request ) {
		$body   = $request->get_json_params();
		$radio  = ! empty( $body['radio'] ) ? sanitize_text_field( $body['radio'] ) : null;
		$value  = ! empty( $body['value'] ) ? sanitize_text_field( $body['value'] ) : null;
		$radios = aioseo()->settings->toggledRadio;
		if ( array_key_exists( $radio, $radios ) ) {
			$radios[ $radio ] = $value;
			aioseo()->settings->toggledRadio = $radios;
		}

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Dismisses the upgrade bar.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function hideUpgradeBar() {
		aioseo()->settings->showUpgradeBar = false;

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Hides the Setup Wizard CTA.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function hideSetupWizard() {
		aioseo()->settings->showSetupWizard = false;

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Save options from the front end.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function saveChanges( $request ) {
		$body           = $request->get_json_params();
		$options        = ! empty( $body['options'] ) ? $body['options'] : [];
		$dynamicOptions = ! empty( $body['dynamicOptions'] ) ? $body['dynamicOptions'] : [];
		$network        = ! empty( $body['network'] ) ? (bool) $body['network'] : false;

		// If this is the network admin, reset the options.
		if ( $network ) {
			aioseo()->options->initNetwork();
		}

		aioseo()->options->sanitizeAndSave( $options );
		aioseo()->dynamicOptions->sanitizeAndSave( $dynamicOptions );

		// Re-initialize notices.
		aioseo()->notices->init();

		return new \WP_REST_Response( [
			'success'       => true,
			'notifications' => Models\Notification::getNotifications()
		], 200 );
	}

	/**
	 * Reset settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response The response.
	 */
	public static function resetSettings( $request ) {
		$body     = $request->get_json_params();
		$settings = ! empty( $body['settings'] ) ? $body['settings'] : [];

		$notAllowedOptions = aioseo()->access->getNotAllowedOptions();

		foreach ( $settings as $setting ) {
			$optionAccess = in_array( $setting, [ 'robots', 'blocker' ], true ) ? 'tools' : $setting;

			if ( in_array( $optionAccess, $notAllowedOptions, true ) ) {
				continue;
			}

			switch ( $setting ) {
				case 'robots':
					aioseo()->options->tools->robots->reset();
					break;
				case 'blocker':
					aioseo()->options->deprecated->tools->blocker->reset();
					break;
				default:
					if ( aioseo()->options->has( $setting ) ) {
						aioseo()->options->$setting->reset();
					}
					if ( aioseo()->dynamicOptions->has( $setting ) ) {
						aioseo()->dynamicOptions->$setting->reset();
					}
			}

			if ( 'access-control' === $setting ) {
				aioseo()->access->addCapabilities();
			}
		}

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Import settings from external file.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function importSettings( $request ) {
		$file = $request->get_file_params()['file'];
		if (
			empty( $file['tmp_name'] ) ||
			empty( $file['type'] ) ||
			(
				'application/json' !== $file['type'] &&
				'application/octet-stream' !== $file['type']
			)
		) {
			return new \WP_REST_Response( [
				'success' => false
			], 400 );
		}

		$contents = aioseo()->core->fs->getContents( $file['tmp_name'] );
		if ( 'application/json' === $file['type'] ) {
			// Since this could be any file, we need to pretend like every variable here is missing.
			$contents = json_decode( $contents, true );
			if ( empty( $contents ) ) {
				return new \WP_REST_Response( [
					'success' => false
				], 400 );
			}

			if ( ! empty( $contents['settings'] ) ) {
				// Clean up the array removing options the user should not manage.
				$notAllowedOptions    = aioseo()->access->getNotAllowedOptions();
				$contents['settings'] = array_diff_key( $contents['settings'], $notAllowedOptions );
				if ( ! empty( $contents['settings']['deprecated'] ) ) {
					$contents['settings']['deprecated'] = array_diff_key( $contents['settings']['deprecated'], $notAllowedOptions );
				}

				// Remove any dynamic options and save them separately since this has been refactored.
				$commonDynamic = [
					'sitemap',
					'searchAppearance',
					'breadcrumbs',
					'accessControl'
				];

				foreach ( $commonDynamic as $cd ) {
					if ( ! empty( $contents['settings'][ $cd ]['dynamic'] ) ) {
						$contents['settings']['dynamic'][ $cd ] = $contents['settings'][ $cd ]['dynamic'];
						unset( $contents['settings'][ $cd ]['dynamic'] );
					}
				}

				// These options have a very different structure so we'll do them separately.
				if ( ! empty( $contents['settings']['social']['facebook']['general']['dynamic'] ) ) {
					$contents['settings']['dynamic']['social']['facebook']['general'] = $contents['settings']['social']['facebook']['general']['dynamic'];
					unset( $contents['settings']['social']['facebook']['general']['dynamic'] );
				}

				if ( ! empty( $contents['settings']['dynamic'] ) ) {
					aioseo()->dynamicOptions->sanitizeAndSave( $contents['settings']['dynamic'] );
					unset( $contents['settings']['dynamic'] );
				}

				aioseo()->options->sanitizeAndSave( $contents['settings'] );
			}

			if ( ! empty( $contents['postOptions'] ) ) {
				$notAllowedFields = aioseo()->access->getNotAllowedPageFields();
				foreach ( $contents['postOptions'] as $postData ) {
					// Posts.
					if ( ! empty( $postData['posts'] ) ) {
						foreach ( $postData['posts'] as $post ) {
							unset( $post['id'] );
							// Clean up the array removing fields the user should not manage.
							$post    = array_diff_key( $post, $notAllowedFields );
							$thePost = Models\Post::getPost( $post['post_id'] );
							$thePost->set( $post );
							$thePost->save();
						}
					}
				}
			}
		}

		if ( 'application/octet-stream' === $file['type'] ) {
			$response = aioseo()->importExport->importIniData( $contents );
			if ( ! $response ) {
				return new \WP_REST_Response( [
					'success' => false
				], 400 );
			}
		}

		return new \WP_REST_Response( [
			'success' => true,
			'options' => aioseo()->options->all()
		], 200 );
	}

	/**
	 * Export settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function exportSettings( $request ) {
		$body        = $request->get_json_params();
		$settings    = ! empty( $body['settings'] ) ? $body['settings'] : [];
		$postOptions = ! empty( $body['postOptions'] ) ? $body['postOptions'] : [];
		$allSettings = [
			'settings'    => [],
			'postOptions' => []
		];

		if ( ! empty( $settings ) ) {
			$options           = aioseo()->options->noConflict();
			$dynamicOptions    = aioseo()->dynamicOptions->noConflict();
			$notAllowedOptions = aioseo()->access->getNotAllowedOptions();
			foreach ( $settings as $setting ) {
				$optionAccess = in_array( $setting, [ 'robots', 'blocker' ], true ) ? 'tools' : $setting;

				if ( in_array( $optionAccess, $notAllowedOptions, true ) ) {
					continue;
				}

				switch ( $setting ) {
					case 'robots':
						$allSettings['settings']['tools']['robots'] = $options->tools->robots->all();
						break;
					default:
						if ( $options->has( $setting ) ) {
							$allSettings['settings'][ $setting ] = $options->$setting->all();
						}

						// If there are related dynamic settings, let's include them.
						if ( $dynamicOptions->has( $setting ) ) {
							$allSettings['settings']['dynamic'][ $setting ] = $dynamicOptions->$setting->all();
						}

						// It there is a related deprecated $setting, include it.
						if ( $options->deprecated->has( $setting ) ) {
							$allSettings['settings']['deprecated'][ $setting ] = $options->deprecated->$setting->all();
						}
						break;
				}
			}
		}

		if ( ! empty( $postOptions ) ) {
			$notAllowedFields = aioseo()->access->getNotAllowedPageFields();
			foreach ( $postOptions as $postType ) {
				$posts = aioseo()->core->db->start( 'aioseo_posts as ap' )
					->select( 'ap.*' )
					->join( 'posts as p', 'ap.post_id = p.ID' )
					->where( 'p.post_type', $postType )
					->run()
					->result();

				foreach ( $posts as $post ) {
					// Clean up the array removing fields the user should not manage.
					$post = array_diff_key( (array) $post, $notAllowedFields );
					if ( count( $post ) > 2 ) {
						$allSettings['postOptions'][ $postType ]['posts'][] = $post;
					}
				}
			}
		}

		return new \WP_REST_Response( [
			'success'  => true,
			'settings' => $allSettings
		], 200 );
	}

	/**
	 * Import other plugin settings.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function importPlugins( $request ) {
		$body     = $request->get_json_params();
		$plugins  = ! empty( $body['plugins'] ) ? $body['plugins'] : [];

		foreach ( $plugins as $plugin ) {
			aioseo()->importExport->startImport( $plugin['plugin'], $plugin['settings'] );
		}

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}

	/**
	 * Executes a given administrative task.
	 *
	 * @since 4.1.2
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function doTask( $request ) {
		$body   = $request->get_json_params();
		$action = ! empty( $body['action'] ) ? $body['action'] : '';

		switch ( $action ) {
			case 'clear-cache':
				aioseo()->core->cache->clear();
				break;
			case 'readd-capabilities':
				aioseo()->access->addCapabilities();
				break;
			case 'rerun-migrations':
				aioseo()->internalOptions->database->installedTables   = '';
				aioseo()->internalOptions->internal->lastActiveVersion = '4.0.0';
				break;
			case 'remove-duplicates':
				aioseo()->updates->removeDuplicateRecords();
				break;
			case 'unescape-data':
				aioseo()->admin->scheduleUnescapeData();
				break;
			case 'clear-image-data':
				aioseo()->sitemap->query->resetImages();
				break;
			case 'restart-v3-migration':
				Migration\Helpers::redoMigration();
				break;
			default:
				return new \WP_REST_Response( [
					'success' => true,
					'error'   => 'The given action isn\'t defined.'
				], 400 );
		}

		return new \WP_REST_Response( [
			'success' => true
		], 200 );
	}
}