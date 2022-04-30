<?php


function seedprod_lite_check_for_free_version() {
	try {
		$seedprod_unsupported_feature = array();
		$migration                    = get_option( 'seedprod_migration_run_once' );
		if ( empty( $migration ) || ! empty( $_GET['sp-force-migrate'] ) ) {

			// migrate old licnese key if available
			$old_key = get_option( 'seed_cspv5_license_key' );
			if ( ! empty( $old_key ) ) {
				update_option( 'seedprod_api_key', $old_key );
				$r = seedprod_lite_save_api_key( $old_key );
			}

			// see if free version old settings exists and they do not have the pro version
			// && empty(get_option('seed_cspv5_settings_content'))
			if ( ! empty( $_GET['sp-force-migrate'] ) || empty( get_option( 'seed_cspv5_settings_content' ) ) && empty( get_option( 'seedprod_coming_soon_page_id' ) ) && empty( get_option( 'seedprod_maintenance_mode_page_id' ) ) && ! empty( get_option( 'seed_csp4_settings_content' ) ) && get_option( 'seedprod_csp4_migrated' ) === false && get_option( 'seedprod_csp4_imported' ) === false ) {

				// import csp4 settings to plugin

				// get settings
				$s1 = get_option( 'seed_csp4_settings_content' );
				$s2 = get_option( 'seed_csp4_settings_design' );
				$s3 = get_option( 'seed_csp4_settings_advanced' );

				if ( empty( $s1 ) ) {
					$s1 = array();
				}

				if ( empty( $s2 ) ) {
					$s2 = array();
				}

				if ( empty( $s3 ) ) {
					$s3 = array();
				}

				$csp4_settings = $s1 + $s2 + $s3;

				// update global settings

				$ts                = get_option( 'seedprod_settings' );
				$seedprod_settings = json_decode( $ts, true );

				$type = 'cs';
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 1 ) {
					$seedprod_settings['enable_coming_soon_mode'] = true;
					$seedprod_settings['enable_maintenance_mode'] = false;
					$type = 'cs';
				}
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 2 ) {
					$seedprod_settings['enable_maintenance_mode'] = true;
					$seedprod_settings['enable_coming_soon_mode'] = false;
					$type = 'mm';
				}

				update_option( 'seedprod_settings', json_encode( $seedprod_settings ) );

				// update page settings
				$csp4_template_file = SEEDPROD_PLUGIN_PATH . 'app/backwards/csp4-template.json';
				$csp4_template      = json_decode( file_get_contents( $csp4_template_file ), true );

				//$csp4_template
				// page to publish if active from v4
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 1 || $csp4_settings['status'] == 2 ) {
					$csp4_template['post_status'] = 'published';
				}

				// set page type
				$csp4_template['page_type'] = $type;

				// set custom html
				if ( ! empty( $csp4_settings['html'] ) ) {
					$custom_html = json_decode(
						'{
                "id": "iuf8h9",
                "elType": "block",
                "type": "custom-html",
                "settings": {
                    "code": "Full Page Custom HTML is no longer supported in this builder. However your custom html page is still being display and will continue to be displayed as long as you DO NOT save this page. There is Custom HTML block you can use in the builder.",
                    "marginTop": "0",
                    "paddingTop": "",
                    "paddingBottom": "",
                    "paddingLeft": "",
                    "paddingRight": "",
                    "paddingSync": true
                }}
            '
					);
					if ( ! empty( $custom_html ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks']   = array();
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][] = $custom_html;
					}

					$csp4_template['document']['settings']['contentPosition']             = '1';
					$csp4_template['document']['sections'][0]['settings']['contentWidth'] = '1';
				} else {

					// set logo
					if ( ! empty( $csp4_settings['logo'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['src'] = $csp4_settings['logo'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['src'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z/C/HgAGgwJ/lK3Q6wAAAABJRU5ErkJggg==';
					}

					// set headline
					if ( ! empty( $csp4_settings['headline'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['headerTxt'] = $csp4_settings['headline'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['headerTxt'] = '';
					}

					// set description
					if ( ! empty( $csp4_settings['description'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][2]['settings']['txt'] = $csp4_settings['description'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][2]['settings']['txt'] = '';
					}

					// set footer credit
					if ( ! empty( $csp4_settings['footer_credit'] ) ) {
						$csp4_template['show_powered_by_link'] = true;
					}

					// favicon
					if ( ! empty( $csp4_settings['favicon'] ) ) {
						$csp4_template['favicon'] = $csp4_settings['favicon'];
					}

					// title
					if ( ! empty( $csp4_settings['seo_title'] ) ) {
						$csp4_template['seo_title'] .= $csp4_settings['seo_title'];
					}

					// meta
					if ( ! empty( $csp4_settings['seo_description'] ) ) {
						$csp4_template['seo_description'] .= $csp4_settings['seo_description'];
					}

					// set google analytics
					if ( ! empty( $csp4_settings['ga_analytics'] ) ) {
						$csp4_template['footer_scripts'] = $csp4_settings['ga_analytics'];
					}

					// set bg color
					if ( ! empty( $csp4_settings['bg_color'] ) ) {
						$csp4_template['document']['settings']['bgColor'] = $csp4_settings['bg_color'];
					}

					// set bg dimming
					if ( ! empty( $csp4_settings['bg_overlay'] ) ) {
						$csp4_template['document']['settings']['bgDimming'] = '50';
					}

					// set bg image
					if ( ! empty( $csp4_settings['bg_image'] ) ) {
						$csp4_template['document']['settings']['bgImage'] = $csp4_settings['bg_image'];
					}

					// set bg cover
					if ( ! empty( $csp4_settings['bg_cover'] ) ) {
						if ( ! empty( $csp4_settings['bg_size'] ) && $csp4_settings['bg_size'] == 'cover' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'cover';
						}

						if ( ! empty( $csp4_settings['bg_size'] ) && $csp4_settings['bg_size'] == 'contain' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'full';
						}
					} else {
						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeat';
						}

						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat-x' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeattop';
						}

						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat-y' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeatvc';
						}
					}

					//$csp4_template['document']['settings']['customCss'] .=

					// set width
					if ( ! empty( $csp4_settings['max_width'] ) ) {
						$csp4_template['document']['sections'][0]['settings']['width'] = $csp4_settings['max_width'];
					}

					// enable well
					if ( ! empty( $csp4_settings['enable_well'] ) ) {
						$csp4_template['document']['sections'][0]['settings']['bgColor']        = '#ffffff';
						$csp4_template['document']['sections'][0]['settings']['borderRadiusTL'] = '4';
					}

					// set text color
					if ( ! empty( $csp4_settings['text_color'] ) ) {
						$csp4_template['document']['settings']['textColor'] = $csp4_settings['text_color'];
					}

					// set headline color
					if ( ! empty( $csp4_settings['headline_color'] ) ) {
						$csp4_template['document']['settings']['headerColor'] = $csp4_settings['headline_color'];
					} else {
						$csp4_template['document']['settings']['headerColor'] = $csp4_settings['text_color'];
					}

					// set link color
					if ( ! empty( $csp4_settings['link_color'] ) ) {
						$csp4_template['document']['settings']['linkColor']   = $csp4_settings['link_color'];
						$csp4_template['document']['settings']['buttonColor'] = $csp4_settings['link_color'];
					}

					// set font
					if ( ! empty( $csp4_settings['text_font'] ) ) {
						$csp4_template['document']['settings']['textFontVariant']   = '400';
						$csp4_template['document']['settings']['headerFontVariant'] = '400';

						if ( $csp4_settings['text_font'] == '_arial' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_arial_black' ) {
							$csp4_template['document']['settings']['textFont']          = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont']        = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['textFontVariant']   = '700';
							$csp4_template['document']['settings']['headerFontVariant'] = '700';
						}
						if ( $csp4_settings['text_font'] == '_georgia' ) {
							$csp4_template['document']['settings']['textFont']   = 'Georgia, serif';
							$csp4_template['document']['settings']['headerFont'] = 'Georgia, serif';
						}
						if ( $csp4_settings['text_font'] == '_helvetica_neue' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_impact' ) {
							$csp4_template['document']['settings']['textFont']   = 'Impact, Charcoal, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Impact, Charcoal, sans-serif';
						}
						if ( $csp4_settings['text_font'] == '_lucida' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_palatino' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_tahoma' ) {
							$csp4_template['document']['settings']['textFont']   = 'Tahoma, Geneva, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Tahoma, Geneva, sans-serif';
						}
						if ( $csp4_settings['text_font'] == '_times' ) {
							$csp4_template['document']['settings']['textFont']   = "'Times New Roman', Times, serif";
							$csp4_template['document']['settings']['headerFont'] = "'Times New Roman', Times, serif";
						}
						if ( $csp4_settings['text_font'] == '_trebuchet' ) {
							$csp4_template['document']['settings']['textFont']   = "'Trebuchet MS', Helvetica, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Trebuchet MS', Helvetica, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_verdana' ) {
							$csp4_template['document']['settings']['textFont']   = 'Verdana, Geneva, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Verdana, Geneva, sans-serif';
						}
					}

					// set custom css
					if ( ! empty( $csp4_settings['custom_css'] ) ) {
						$csp4_template['document']['settings']['customCss'] .= $csp4_settings['custom_css'];
					}

					// set exclude urls
					if ( ! empty( $csp4_settings['disable_default_excluded_urls'] ) ) {
						$csp4_template['disable_default_excluded_urls'] = true;
					}

					// set header scripts
					if ( ! empty( $csp4_settings['header_scripts'] ) ) {
						$csp4_template['header_scripts'] .= $csp4_settings['header_scripts'];
					}

					// set footer scripts
					if ( ! empty( $csp4_settings['footer_scripts'] ) ) {
						$csp4_template['footer_scripts'] .= $csp4_settings['footer_scripts'];
					}

					// set append html
					if ( ! empty( $csp4_settings['append_html'] ) ) {
						$append_html = json_decode(
							'{
                "id": "iuf8h9",
                "elType": "block",
                "type": "custom-html",
                "settings": {
                    "code": "' . $csp4_settings['append_html'] . '",
                    "marginTop": "0",
                    "paddingTop": "",
                    "paddingBottom": "",
                    "paddingLeft": "",
                    "paddingRight": "",
                    "paddingSync": true
                }}
           '
						);
						if ( ! empty( $append_html ) ) {
							$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][] = $append_html;
						}
					}
				}

				// create the coming soon or maintenance page and inject settings
				$slug = '';
				$cpt  = 'page';
				if ( $type == 'cs' || $type == 'mm' || $type == 'p404' ) {
					$cpt = 'seedprod';
				}
				if ( $type == 'cs' ) {
					$slug = 'sp-cs';
				}
				if ( $type == 'mm' ) {
					$slug = 'sp-mm';
				}

				$id = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => '',
						'post_status'    => 'publish',
						'post_title'     => 'seedprod',
						'post_type'      => $cpt,
						'post_name'      => $slug,
						'meta_input'     => array(
							'_seedprod_page'      => true,
							'_seedprod_page_uuid' => wp_generate_uuid4(),
						),
					),
					true
				);

				// update post because wp screws our json settings
				global $wpdb;
				$tablename = $wpdb->prefix . 'posts';
				$r         = $wpdb->update(
					$tablename,
					array(
						'post_content_filtered' => json_encode( $csp4_template ),
					),
					array( 'ID' => $id ),
					array(
						'%s',
					),
					array( '%d' )
				);

				if ( $type == 'cs' ) {
					update_option( 'seedprod_coming_soon_page_id', $id );
				}
				if ( $type == 'mm' ) {
					update_option( 'seedprod_maintenance_mode_page_id', $id );
				}

				// do we need to show it?
				update_option( 'seedprod_csp4_imported', true );
				update_option( 'seedprod_show_csp4', true );
				// flush rewrite rules
				flush_rewrite_rules();
			}

			update_option( 'seedprod_migration_run_once', true );
		}
	} catch ( Exception $e ) {
		return $e;
	}
}

