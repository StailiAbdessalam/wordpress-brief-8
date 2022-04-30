<?php


/**
 * Welcome Page On Activation
 */
add_action( 'admin_init', 'seedprod_lite_welcome_screen_do_activation_redirect' );

/**
 * Welcome screen for activation redirect.
 *
 * @return void
 */
function seedprod_lite_welcome_screen_do_activation_redirect() {
	// Check PHP Version
	if ( version_compare( phpversion(), '5.3.3', '<=' ) ) {
		wp_die( esc_html__( "The minimum required version of PHP to run this plugin is PHP Version 5.3.3. Please contact your hosting company and ask them to upgrade this site's php verison.", 'coming-soon' ), esc_html__( 'Upgrade PHP', 'coming-soon' ), 200 );
	}

	// Bail if no activation redirect
	if ( ! get_transient( '_seedprod_welcome_screen_activation_redirect' ) ) {
		return;
	}

	// Delete the redirect transient
	delete_transient( '_seedprod_welcome_screen_activation_redirect' );

	// Bail if activating from network, or bulk
	$activate_multi = isset( $_GET['activate-multi'] ) ? sanitize_text_field( wp_unslash( $_GET['activate-multi'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( is_network_admin() || null !== $activate_multi ) {
		return;
	}

	// Redirect to our page
	wp_safe_redirect( add_query_arg( array( 'page' => 'seedprod_lite' ), admin_url( 'admin.php' ) ) . '#/welcome' );
}


/**
 * Save API Key
 */
function seedprod_lite_save_api_key( $api_key = null ) {
	if ( check_ajax_referer( 'seedprod_nonce', '_wpnonce', false ) || ! empty( $api_key ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_license_capability', 'manage_options' ) ) ) {
			wp_send_json_error();
		}

		if ( empty( $api_key ) ) {
			$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : null;
		}

		if ( defined( 'SEEDPROD_LOCAL_JS' ) ) {
			$slug = 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php';
		} else {
			$slug = SEEDPROD_SLUG;
		}

		$token = get_option( 'seedprod_token' );
		if ( empty( $token ) ) {
			add_option( 'seedprod_token', wp_generate_uuid4() );
		}

		// Validate the api key
		$data = array(
			'action'            => 'info',
			'license_key'       => $api_key,
			'token'             => get_option( 'seedprod_token' ),
			'wp_version'        => get_bloginfo( 'version' ),
			'domain'            => home_url(),
			'installed_version' => SEEDPROD_VERSION,
			'slug'              => $slug,
		);

		if ( empty( $data['license_key'] ) ) {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'License Key is Required.', '' ),
			);
			wp_send_json( $response );
			exit;
		}

		$headers = array();

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Accept' => 'application/json',
			)
		);

		$url      = SEEDPROD_API_URL . 'update';
		$response = wp_remote_post(
			$url,
			array(
				'body'    => $data,
				'headers' => $headers,
			)
		);

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response->get_error_message(),
			);
			wp_send_json( $response );
		}

		if ( 200 !== $status_code ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response['response']['message'],
			);
			wp_send_json( $response );
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! empty( $body ) ) {
			$body = json_decode( $body );
		}

		if ( ! empty( $body->valid ) && true === $body->valid ) {
			// Store API key
			update_option( 'seedprod_user_id', $body->user_id );
			update_option( 'seedprod_api_token', $body->api_token );
			update_option( 'seedprod_api_key', $data['license_key'] );
			update_option( 'seedprod_api_message', $body->message );
			update_option( 'seedprod_license_name', $body->license_name );
			update_option( 'seedprod_a', true );
			update_option( 'seedprod_per', $body->per );
			$response = array(
				'status'       => 'true',
				/* translators: 1. License name.*/
				'license_name' => sprintf( __( 'You currently have the <strong>%s</strong> license.', 'coming-soon' ), $body->license_name ),
				'msg'          => $body->message,
				'body'         => $body,
			);
		} elseif ( isset( $body->valid ) && false === $body->valid ) {
			$api_msg = __( 'Invalid License Key.', 'coming-soon' );
			if ( 'Unauthenticated.' != $body->message ) {
				$api_msg = $body->message;
			}
			update_option( 'seedprod_license_name', '' );
			update_option( 'seedprod_api_token', '' );
			update_option( 'seedprod_api_key', '' );
			update_option( 'seedprod_api_message', $api_msg );
			update_option( 'seedprod_a', false );
			update_option( 'seedprod_per', '' );
			$response = array(
				'status'       => 'false',
				'license_name' => '',
				'msg'          => $api_msg,
				'body'         => $body,
			);
		}

		// Send Response
		if ( ! empty( $_POST['api_key'] ) ) {
			wp_send_json( $response );
			exit;
		} else {
			return $response;
		}
	}
}

